<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Settings;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Services\SubscriptionLifecycleService;

class SystemAdminController extends Controller
{
    /**
     * Display the system administration settings page.
     */
    public function settings()
    {
        // Use the settings view when it exists.
        if (view()->exists('admin.settings')) {
            return view('admin.settings', [
                'settings' => array_replace(Settings::DEFAULTS, Settings::cached()),
            ]);
        }

        // Prevent another error if the Blade view has not been created.
        return response()->view('admin.settings-placeholder', [
            'message' => 'The system settings page is not configured yet.',
        ], 200);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:80'],
            'default_locale' => ['required', Rule::in(['en', 'fr', 'rw', 'sw'])],
            'currency' => ['required', 'string', 'size:3', 'alpha'],
            'support_email' => ['nullable', 'email', 'max:255'],
            'support_phone' => ['nullable', 'string', 'max:30'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'trial_days' => ['required', 'integer', 'between:0,365'],
            'registration_open' => ['required', 'boolean'],
            'maintenance_mode' => ['required', 'boolean'],
            'maintenance_message' => ['nullable', 'string', 'max:500'],
        ]);

        foreach (Settings::ALLOWED as $key) {
            $value = $validated[$key] ?? null;
            Settings::query()->updateOrCreate(
                ['key' => $key],
                ['value' => is_null($value) ? null : (string) $value],
            );
        }

        Settings::flush();

        return redirect()
            ->route('admin.settings')
            ->with('success', 'System settings saved successfully.');
    }

    /**
     * System admin dashboard: KPIs, 12-month trends, status mix,
     * subscription breakdown and platform health.
     */
    public function dashboard()
    {
        /* ---------------------------------------------------------------
         | Core counts (unchanged)
         * ------------------------------------------------------------- */
        $totalShops      = Shop::count();
        $pendingShops    = Shop::where('status', 'pending')->count();
        $approvedShops   = Shop::where('status', 'approved')->count();
        $rejectedShops   = Shop::where('status', 'rejected')->count();
        $totalUsers      = User::count();
        $unassignedUsers = User::whereNull('shop_id')->count();

        $pendingShopsList = Shop::with('users')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $recentShops = Shop::withCount('users')
            ->latest()
            ->take(5)
            ->get();

        $subscriptionPlans = SubscriptionPlan::all();
        $subscriptionPaymentsThisMonth = SubscriptionPayment::query()
            ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()]);
        $subscriptionRevenueThisMonth = (float) (clone $subscriptionPaymentsThisMonth)->sum('amount');
        $subscriptionPaymentCountThisMonth = (clone $subscriptionPaymentsThisMonth)->count();
        $subscriptionRevenueTotal = (float) SubscriptionPayment::query()->sum('amount');

        /* ---------------------------------------------------------------
         | 12-month trend series
         * ------------------------------------------------------------- */
        $windowStart = now()->startOfMonth()->subMonths(11);
        $monthKeys   = [];
        $monthLabels = [];

        for ($i = 0; $i < 12; $i++) {
            $m             = $windowStart->copy()->addMonths($i);
            $monthKeys[]   = $m->format('Y-m');
            $monthLabels[] = $m->format('M');
        }

        $monthlySeries = function (string $model) use ($windowStart, $monthKeys): array {
            $counts = $model::query()
                ->where('created_at', '>=', $windowStart)
                ->pluck('created_at')
                ->filter()
                ->countBy(fn ($date) => Carbon::parse($date)->format('Y-m'));

            return array_map(fn ($key) => (int) ($counts[$key] ?? 0), $monthKeys);
        };

        $cumulative = function (array $series, int $baseline): array {
            $running = $baseline;

            return array_map(function ($value) use (&$running) {
                $running += $value;

                return $running;
            }, $series);
        };

        $shopSeries = $monthlySeries(Shop::class);
        $userSeries = $monthlySeries(User::class);

        $shopCumulative = $cumulative($shopSeries, Shop::where('created_at', '<', $windowStart)->count());
        $userCumulative = $cumulative($userSeries, User::where('created_at', '<', $windowStart)->count());

        $change = function (array $series): ?float {
            $current  = (int) end($series);
            $previous = (int) ($series[count($series) - 2] ?? 0);

            if ($previous === 0) {
                return $current > 0 ? 100.0 : null;
            }

            return round((($current - $previous) / $previous) * 100, 1);
        };

        $shopTrend = $change($shopSeries);
        $userTrend = $change($userSeries);

        $newShopsThisMonth = (int) end($shopSeries);
        $newUsersThisMonth = (int) end($userSeries);

        /* ---------------------------------------------------------------
         | Distribution data
         * ------------------------------------------------------------- */
        $statusBreakdown = [
            'approved' => $approvedShops,
            'pending'  => $pendingShops,
            'rejected' => $rejectedShops,
            'other'    => max($totalShops - $approvedShops - $pendingShops - $rejectedShops, 0),
        ];

        $roleBreakdown = User::query()
            ->select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->orderByDesc('total')
            ->pluck('total', 'role');

        $suspendedUsers = Schema::hasColumn('users', 'account_status')
            ? User::where('account_status', 'suspended')->count()
            : 0;

        /* ---------------------------------------------------------------
         | Subscriptions
         * ------------------------------------------------------------- */
        $planStats           = collect();
        $mrr                 = 0.0;
        $activeSubscriptions = 0;
        $shopsWithoutPlan    = 0;

        $shopsWithSubscriptions = Shop::query()
            ->with(['subscription.subscriptionPlan'])
            ->get();
        $approvedShopIds = $shopsWithSubscriptions
            ->where('status', 'approved')
            ->modelKeys();
        $currentSubscriptions = $shopsWithSubscriptions
            ->map(fn (Shop $shop) => $shop->subscription)
            ->filter();
        $subscriptionsByPlan = $currentSubscriptions
            ->filter(fn (Subscription $subscription) => $subscription->subscription_plan_id !== null)
            ->groupBy('subscription_plan_id');
        $shopsOnPlans = (int) $subscriptionsByPlan->sum(fn ($subscriptions) => $subscriptions->count());
        $shopsWithoutPlan = max($totalShops - $shopsOnPlans, 0);
        $totalSubscriptionsOnPlans = max($shopsOnPlans, 1);

        $planStats = $subscriptionPlans
            ->map(function ($plan) use ($subscriptionsByPlan, $approvedShopIds, $totalSubscriptionsOnPlans) {
                $subscriptions = $subscriptionsByPlan->get($plan->id, collect());
                $activeSubscriptions = $subscriptions->filter(fn (Subscription $subscription) =>
                    $subscription->status === 'active'
                    && $subscription->ends_at?->isFuture()
                    && in_array((int) $subscription->shop_id, $approvedShopIds, true)
                );
                $monthlyPrice = match ($plan->billing_period) {
                    'quarterly' => (float) $plan->price / 3,
                    'yearly' => (float) $plan->price / 12,
                    default => (float) $plan->price,
                };

                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price' => (float) $plan->price,
                    'billing_cycle' => $plan->billing_period,
                    'status' => $plan->is_active ? 'active' : 'inactive',
                    'max_users' => $plan->max_users,
                    'max_products' => $plan->max_products,
                    'max_branches' => $plan->max_branches,
                    'shops' => $subscriptions->count(),
                    'approved' => $activeSubscriptions->count(),
                    'share' => round($subscriptions->count() / $totalSubscriptionsOnPlans * 100, 1),
                    'mrr' => round($activeSubscriptions->count() * $monthlyPrice, 2),
                ];
            })
            ->sortByDesc('shops')
            ->values();

        $mrr = (float) $planStats->sum('mrr');
        $activeSubscriptions = (int) $planStats->sum('approved');

        /* ---------------------------------------------------------------
         | Platform health
         * ------------------------------------------------------------- */
        $reviewed     = $approvedShops + $rejectedShops;
        $approvalRate = $reviewed > 0 ? round($approvedShops / $reviewed * 100) : 0;

        $assignmentRate = $totalUsers > 0
            ? round(($totalUsers - $unassignedUsers) / $totalUsers * 100)
            : 0;

        $activeAccountRate = $totalUsers > 0
            ? round(($totalUsers - $suspendedUsers) / $totalUsers * 100)
            : 0;

        $planCoverageRate = $totalShops > 0
            ? round(($totalShops - $shopsWithoutPlan) / $totalShops * 100)
            : 0;

        $topShops = Shop::withCount('users')
            ->orderByDesc('users_count')
            ->take(5)
            ->get();

        $currency = config('app.currency', 'RWF');

        return view('dashboard.system-admin', compact(
            'totalShops',
            'pendingShops',
            'approvedShops',
            'rejectedShops',
            'totalUsers',
            'unassignedUsers',
            'pendingShopsList',
            'recentShops',
            'subscriptionPlans',
            'subscriptionRevenueThisMonth',
            'subscriptionPaymentCountThisMonth',
            'subscriptionRevenueTotal',
            'monthLabels',
            'shopSeries',
            'userSeries',
            'shopCumulative',
            'userCumulative',
            'shopTrend',
            'userTrend',
            'newShopsThisMonth',
            'newUsersThisMonth',
            'statusBreakdown',
            'roleBreakdown',
            'suspendedUsers',
            'planStats',
            'mrr',
            'activeSubscriptions',
            'shopsWithoutPlan',
            'approvalRate',
            'assignmentRate',
            'activeAccountRate',
            'planCoverageRate',
            'topShops',
            'currency'
        ));
    }

    public function listUsers()
    {
        $users = User::query()->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    // GET /admin/shops  (admin.shops.index)
    public function shopsIndex(Request $request)
    {
        $filter = $request->query('subscription', 'all');
        abort_unless(in_array($filter, ['all', 'active', 'expiring', 'expired'], true), 404);

        $query = Shop::query()
            ->with(['creator', 'subscription.subscriptionPlan'])
            ->when($filter === 'active', fn ($query) =>
                $query->whereHas('subscription', fn ($subscriptions) =>
                    $subscriptions->whereIn('status', ['trial', 'active'])
                        ->where('ends_at', '>=', now())
                )
            )
            ->when($filter === 'expiring', fn ($query) =>
                $query->whereHas('subscription', fn ($subscriptions) =>
                    $subscriptions->whereIn('status', ['trial', 'active'])
                        ->whereBetween('ends_at', [now(), now()->addDays(14)])
                )
            )
            ->when($filter === 'expired', fn ($query) =>
                $query->whereHas('subscription', fn ($subscriptions) =>
                    $subscriptions->where(fn ($query) =>
                        $query->whereIn('status', ['past_due', 'expired', 'suspended'])
                            ->orWhere('ends_at', '<', now())
                    )
                )
            );

        $shops = $query->latest()->paginate(20)->withQueryString();
        $currentSubscriptions = Shop::with('subscription.subscriptionPlan')
            ->get()
            ->pluck('subscription')
            ->filter();
        $mrr = $currentSubscriptions
            ->where('status', 'active')
            ->filter(fn ($subscription) => $subscription->ends_at?->isFuture())
            ->sum(function ($subscription) {
                $price = (float) ($subscription->subscriptionPlan?->price ?? 0);

                return match ($subscription->subscriptionPlan?->billing_period) {
                    'quarterly' => $price / 3,
                    'yearly' => $price / 12,
                    default => $price,
                };
            });
        $expiringThisMonth = $currentSubscriptions->filter(fn ($subscription) =>
            in_array($subscription->status, ['trial', 'active'], true)
                && $subscription->ends_at?->between(now(), now()->endOfMonth())
        )->count();
        $plans = SubscriptionPlan::query()->where('is_active', true)->orderBy('price')->get();

        return view('admin.shops.index', compact('shops', 'filter', 'mrr', 'expiringThisMonth', 'plans'));
    }

    // POST /admin/shops  (admin.shops.store)
    public function storeShop(
        Request $request,
        SubscriptionLifecycleService $subscriptions
    ): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')
                    ->whereNull('shop_id')
                    ->where('role', '!=', 'system_admin'),
            ],
            'business_name' => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255', 'unique:shops,registration_number'],
            'tin_number' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:shops,email'],
            'phone' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'subscription_plan_id' => [
                'nullable',
                'integer',
                Rule::exists('subscription_plans', 'id')->where('is_active', true),
            ],
            'status' => ['required', Rule::in(['approved', 'pending'])],
        ]);

        $user = User::query()->findOrFail($validated['user_id']);
        $registrationNumber = $validated['registration_number'] ?: Str::upper(Str::random(10));
        while (Shop::query()->where('registration_number', $registrationNumber)->exists()) {
            $registrationNumber = Str::upper(Str::random(10));
        }
        $logo = $request->file('logo')?->store('shop-logos', 'public');

        DB::transaction(function () use ($validated, $user, $registrationNumber, $logo, $subscriptions): void {
            $approved = $validated['status'] === 'approved';
            $shop = Shop::create([
                'business_name' => $validated['business_name'],
                'business_type' => $validated['business_type'],
                'registration_number' => $registrationNumber,
                'tin_number' => $validated['tin_number'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'country' => $validated['country'],
                'city' => $validated['city'],
                'address' => $validated['address'],
                'logo' => $logo,
                'slug' => Str::slug($validated['business_name']) . '-' . Str::lower(Str::random(6)),
                'status' => $validated['status'],
                'created_by' => $user->id,
                'approved_by' => $approved ? auth()->id() : null,
                'approved_at' => $approved ? now() : null,
            ]);

            if (! empty($validated['subscription_plan_id'])) {
                $plan = SubscriptionPlan::query()->findOrFail($validated['subscription_plan_id']);
                $subscriptions->assignPlan($shop, $plan);
            }

            $user->update([
                'shop_id' => $shop->id,
                'role' => 'shop_admin',
            ]);
        });

        return redirect()->route('admin.shops.index')
            ->with('success', 'Shop created.');
    }

    // GET /admin/shops/create  (admin.shops.create)
    public function createShop()
    {
        $subscriptionPlans = SubscriptionPlan::query()->orderBy('name')->get();

        $unassignedUsers = User::query()
            ->whereNull('shop_id')
            ->where('role', '!=', 'system_admin')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone', 'country', 'city', 'address']);

        return view('admin.shops.create', compact('subscriptionPlans', 'unassignedUsers'));
    }

    public function approveShop(
        Shop $shop,
        SubscriptionLifecycleService $subscriptions
    ): RedirectResponse
    {
        DB::transaction(function () use ($shop, $subscriptions) {
            $shop->update([
                'status'           => 'approved',
                'approved_by'      => auth()->id(),
                'approved_at'      => now(),
                'rejection_reason' => null,
            ]);

            $subscriptions->activate($shop);
        });

        return redirect()
            ->back()
            ->with('success', 'Shop approved successfully. Its users can now access shop data.');
    }

    public function editUser(User $user)
    {
        $shops = Shop::orderBy('business_name')->get();

        return view('admin.users.edit', compact('user', 'shops'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'role'     => [
                'required',
                Rule::in([
                    'system_admin',
                    'business_owner',
                    'business_admin',
                    'shop_admin',
                    'seller',
                    'waiter',
                    'user',
                ]),
            ],
            'shop_id'        => ['nullable', 'integer', 'exists:shops,id'],
            'account_status' => [
                'required',
                Rule::in(['active', 'suspended']),
            ],
            'locale' => ['required', Rule::in(['en', 'fr', 'rw', 'sw'])],
        ]);

        // Update the user's basic information.
        $user->name           = $validated['name'];
        $user->email          = $validated['email'];
        $user->role           = $validated['role'];
        $user->shop_id        = $validated['shop_id'] ?? null;
        $user->account_status = $validated['account_status'];
        $user->locale         = $validated['locale'];

        // Only change the password when one was entered.
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($user->role === 'business_owner' && $validated['shop_id']) {
            $user->shops()->syncWithoutDetaching([$validated['shop_id']]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }
}
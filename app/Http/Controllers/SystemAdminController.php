<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Settings;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

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

        if (Schema::hasColumn('shops', 'subscription_plan_id')) {
            $allByPlan = Shop::query()
                ->whereNotNull('subscription_plan_id')
                ->select('subscription_plan_id', DB::raw('COUNT(*) as total'))
                ->groupBy('subscription_plan_id')
                ->pluck('total', 'subscription_plan_id');

            $approvedByPlan = Shop::query()
                ->where('status', 'approved')
                ->whereNotNull('subscription_plan_id')
                ->select('subscription_plan_id', DB::raw('COUNT(*) as total'))
                ->groupBy('subscription_plan_id')
                ->pluck('total', 'subscription_plan_id');

            $shopsOnPlans     = (int) $allByPlan->sum();
            $shopsWithoutPlan = Shop::whereNull('subscription_plan_id')->count();

            $planStats = $subscriptionPlans
                ->map(function ($plan) use ($allByPlan, $approvedByPlan, $shopsOnPlans) {
                    $shops    = (int) ($allByPlan[$plan->id] ?? 0);
                    $approved = (int) ($approvedByPlan[$plan->id] ?? 0);
                    $monthly  = ($plan->billing_cycle ?? 'monthly') === 'yearly'
                        ? ((float) $plan->price) / 12
                        : (float) $plan->price;

                    return [
                        'id'            => $plan->id,
                        'name'          => $plan->name,
                        'price'         => (float) $plan->price,
                        'billing_cycle' => $plan->billing_cycle ?? 'monthly',
                        'status'        => $plan->status ?? 'active',
                        'max_users'     => $plan->max_users,
                        'max_products'  => $plan->max_products,
                        'max_branches'  => $plan->max_branches,
                        'shops'         => $shops,
                        'approved'      => $approved,
                        'share'         => $shopsOnPlans > 0 ? round($shops / $shopsOnPlans * 100, 1) : 0,
                        'mrr'           => round($approved * $monthly, 2),
                    ];
                })
                ->sortByDesc('shops')
                ->values();

            $mrr                 = (float) $planStats->sum('mrr');
            $activeSubscriptions = (int) $planStats->sum('approved');
        }

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
    public function shopsIndex()
    {
        $shops = Shop::query()->latest()->paginate(20);

        return view('admin.shops.index', compact('shops'));
    }

    // POST /admin/shops  (admin.shops.store)
    public function storeShop(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // add the other shop columns here
        ]);

        Shop::create($data);

        return redirect()->route('admin.shops.index')
            ->with('status', 'Shop created.');
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

    public function approveShop(Shop $shop): RedirectResponse
    {
        DB::transaction(function () use ($shop) {
            $shop->update([
                'status'           => 'approved',
                'approved_by'      => auth()->id(),
                'approved_at'      => now(),
                'rejection_reason' => null,
            ]);
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

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }
}
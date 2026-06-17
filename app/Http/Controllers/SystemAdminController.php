<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Expense;
use App\Models\SubscriptionPlan;
use App\Models\ShopSubscription;
use App\Models\SubscriptionPayment;
use App\Models\SystemUsageLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SystemAdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ADMIN DASHBOARD
    |--------------------------------------------------------------------------
    */
    public function dashboard()
    {
        $totalShops = Shop::count();
        $pendingShops = Shop::where('status', 'pending')->count();
        $approvedShops = Shop::where('status', 'approved')->count();
        $suspendedShops = Shop::where('status', 'suspended')->count();
 $pendingShopsList = Shop::where('status', 'pending')->latest()->get();
        $totalUsers = User::count();
        $totalProducts = Product::count();

        $totalSales = Sale::sum('total_amount');
        $totalExpenses = Expense::sum('amount');
        $netRevenue = $totalSales - $totalExpenses;

        // Prevent crash if tables don't exist yet
        $subscriptionRevenue = 0;
        $monthlySubscriptionRevenue = 0;
        $recentPayments = collect();
        $activeSubscriptions = 0;
        $expiredSubscriptions = 0;
        $shopSubscriptions = collect();

        if (Schema::hasTable('subscription_payments')) {
            $subscriptionRevenue = SubscriptionPayment::where('status', 'approved')->sum('amount');

            $monthlySubscriptionRevenue = SubscriptionPayment::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('status', 'approved')
                ->sum('amount');

            $recentPayments = SubscriptionPayment::with('shop')
                ->latest()
                ->take(10)
                ->get();
        }

        if (Schema::hasTable('shop_subscriptions')) {
            $activeSubscriptions = ShopSubscription::where('status', 'active')->count();

            $expiredSubscriptions = ShopSubscription::whereDate('end_date', '<', now())->count();

            $shopSubscriptions = ShopSubscription::latest()->take(10)->get();
        }

        $recentShops = Shop::latest()->take(10)->get();

        $unassignedUsers = User::whereDoesntHave('shop')->count();

        return view('dashboard.admin', compact(
            'totalShops',
            'unassignedUsers',
            'pendingShops',
            'approvedShops',
            'suspendedShops',
            'totalUsers',
            'totalProducts',
            'pendingShopsList',
            'totalSales',
            'totalExpenses',
            'netRevenue',
            'subscriptionRevenue',
            'monthlySubscriptionRevenue',
            'activeSubscriptions',
            'expiredSubscriptions',
            'recentShops',
            'recentPayments',
            'shopSubscriptions'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE SHOP
    |--------------------------------------------------------------------------
    */
    public function approveShop(Shop $shop)
    {
        $shop->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        return back()->with('success', 'Shop approved successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT SHOP
    |--------------------------------------------------------------------------
    */
    public function rejectShop(Shop $shop)
    {
        $shop->update(['status' => 'rejected']);

        return back()->with('success', 'Shop rejected successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | SUSPEND SHOP
    |--------------------------------------------------------------------------
    */
    public function suspendShop(Shop $shop)
    {
        $shop->update(['status' => 'suspended']);

        return back()->with('success', 'Shop suspended successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | REACTIVATE SHOP
    |--------------------------------------------------------------------------
    */
    public function reactivateShop(Shop $shop)
    {
        $shop->update(['status' => 'approved']);

        return back()->with('success', 'Shop reactivated successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW ALL SUBSCRIPTIONS
    |--------------------------------------------------------------------------
    */
    public function subscriptions()
    {
        $subscriptions = ShopSubscription::with(['shop', 'subscriptionPlan'])
            ->latest()
            ->paginate(20);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE SUBSCRIPTION PAYMENT
    |--------------------------------------------------------------------------
    */
    public function approvePayment($id)
    {
        $payment = SubscriptionPayment::findOrFail($id);

        $payment->update([
            'status' => 'approved',
            'approved_by' => auth()->id()
        ]);

        $subscription = $payment->shopSubscription;

        $duration = $subscription->subscriptionPlan->billing_cycle === 'yearly' ? 12 : 1;

        $subscription->update([
            'status' => 'active',
            'payment_status' => 'paid',
            'start_date' => now(),
            'end_date' => now()->addMonths($duration)
        ]);

        return back()->with('success', 'Payment approved successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT PAYMENT
    |--------------------------------------------------------------------------
    */
    public function rejectPayment($id)
    {
        $payment = SubscriptionPayment::findOrFail($id);

        $payment->update(['status' => 'rejected']);

        return back()->with('success', 'Payment rejected');
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW PAYMENTS
    |--------------------------------------------------------------------------
    */
    public function payments()
    {
        $payments = SubscriptionPayment::with(['shop', 'subscription'])
            ->latest()
            ->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    /*
    |--------------------------------------------------------------------------
    | SYSTEM USAGE ANALYTICS
    |--------------------------------------------------------------------------
    */
    public function analytics()
    {
        $logs = SystemUsageLog::with('shop')->latest()->paginate(50);

        $mostActiveShops = SystemUsageLog::selectRaw('shop_id, COUNT(*) as total_actions')
            ->groupBy('shop_id')
            ->with('shop')
            ->orderByDesc('total_actions')
            ->take(10)
            ->get();

        $dailyActivities = SystemUsageLog::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.analytics.index', compact('logs', 'mostActiveShops', 'dailyActivities'));
    }

    /*
    |--------------------------------------------------------------------------
    | REVENUE REPORT
    |--------------------------------------------------------------------------
    */
    public function revenueReport()
    {
        $monthlyRevenue = SubscriptionPayment::selectRaw(
                'MONTH(created_at) as month, YEAR(created_at) as year, SUM(amount) as total'
            )
            ->where('status', 'approved')
            ->groupBy('month', 'year')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('admin.reports.revenue', compact('monthlyRevenue'));
    }

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD REVENUE REPORT PDF
    |--------------------------------------------------------------------------
    */
    public function downloadRevenuePdf()
    {
        $payments = SubscriptionPayment::where('status', 'approved')
            ->with('shop')
            ->latest()
            ->get();

        $totalRevenue = $payments->sum('amount');

        $pdf = Pdf::loadView('admin.reports.revenue-pdf', compact('payments', 'totalRevenue'));

        return $pdf->download('platform-revenue-report.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | EXPIRED SUBSCRIPTIONS
    |--------------------------------------------------------------------------
    */
    public function expiredSubscriptions()
    {
        $subscriptions = ShopSubscription::with(['shop', 'subscriptionPlan'])
            ->whereDate('end_date', '<', now())
            ->paginate(20);

        return view('admin.subscriptions.expired', compact('subscriptions'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE SHOP (FORM)
    |--------------------------------------------------------------------------
    */
   public function createShop()
{
    $unassignedUsers = User::whereDoesntHave('shop')->get();
    $subscriptionPlans = SubscriptionPlan::all();

    return view('admin.shops.create', compact('unassignedUsers', 'subscriptionPlans'));
}

public function storeShop(Request $request)
{
    $validated = $request->validate([
        'business_name'        => 'required|string|max:255',
        'business_type'        => 'required|string|max:255',
        'registration_number'  => 'required|string|max:255|unique:shops,registration_number',
        'tin_number'           => 'nullable|string|max:255',
        'email'                => 'required|email|unique:shops,email',
        'phone'                => 'nullable|string|max:255',
        'country'              => 'required|string|max:255',
        'city'                 => 'required|string|max:255',
        'address'              => 'required|string',
        'logo'                 => 'nullable|image|max:2048',
        'subscriptionplan_id'  => 'nullable|exists:subscriptionplans,id',
        'user_id'              => 'required|exists:users,id',
        'status'               => 'required|in:pending,approved,rejected',
    ]);

    $validated['slug'] = \Illuminate\Support\Str::slug($validated['business_name']) . '-' . \Illuminate\Support\Str::random(6);
    $validated['created_by'] = $validated['user_id'];
    unset($validated['user_id']);

    if ($validated['status'] === 'approved') {
        $validated['approved_by'] = auth()->id();
        $validated['approved_at'] = now();
    }

    if ($request->hasFile('logo')) {
        $validated['logo'] = $request->file('logo')->store('shop-logos', 'public');
    }

    Shop::create($validated);

    return redirect()->route('admin.shops.index')->with('success', 'Shop created successfully.');
}
public function shopsIndex()
{
    $shops = Shop::with(['creator', 'subscriptionPlan'])
        ->latest()
        ->paginate(20);

    return view('admin.shops.index', compact('shops'));
}
/*
|--------------------------------------------------------------------------
| LIST USERS
|--------------------------------------------------------------------------
*/
public function listUsers(Request $request)
{
    $users = User::with('shop')
        ->when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        })
        ->when($request->role, function ($query, $role) {
            $query->where('role', $role);
        })
        ->latest()
        ->paginate(20);

    return view('admin.users.index', compact('users'));
}
/*
|--------------------------------------------------------------------------
| SYSTEM SETTINGS
|--------------------------------------------------------------------------
*/
public function settings()
{
    return view('admin.settings');
}

public function updateSettings(Request $request)
{
    // validate and persist settings as needed for your schema
    $request->validate([
        // e.g. 'site_name' => 'required|string|max:255',
    ]);

    return back()->with('success', 'Settings updated successfully.');
}
}
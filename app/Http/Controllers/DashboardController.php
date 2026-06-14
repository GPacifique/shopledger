<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use App\Models\Shop;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
{
    $user = Auth::user();

    return match ($user->role) {
        'system_admin'   => redirect()->route('admin.dashboard'),
        'business_admin' => redirect()->route('business.dashboard'),
        'seller'         => redirect()->route('seller.dashboard'),
        'accountant'     => redirect()->route('accountant.dashboard'),
        default          => $this->userDashboard($request),
    };
}
protected function userDashboard(Request $request)
{
    $shopId = $request->shop_id;

    $salesQuery = Sale::query();
    $purchaseQuery = Purchase::query();
    $expenseQuery = Expense::query();

    if ($shopId) {
        $salesQuery->where('shop_id', $shopId);
        $purchaseQuery->where('shop_id', $shopId);
        $expenseQuery->where('shop_id', $shopId);
    }

    // ---------------- DAILY
    $dailySales = (clone $salesQuery)->whereDate('sale_date', today())->sum('total_amount');
    $dailyPurchases = (clone $purchaseQuery)->whereDate('purchase_date', today())->sum('total_amount');
    $dailyExpenses = (clone $expenseQuery)->whereDate('expense_date', today())->sum('amount');
    $dailyNetProfit = $dailySales - $dailyPurchases - $dailyExpenses;

    // ---------------- WEEKLY
    $weeklySales = (clone $salesQuery)->whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount');
    $weeklyPurchases = (clone $purchaseQuery)->whereBetween('purchase_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount');
    $weeklyExpenses = (clone $expenseQuery)->whereBetween('expense_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount');
    $weeklyNetProfit = $weeklySales - $weeklyPurchases - $weeklyExpenses;

    // ---------------- YEARLY
    $yearlySales = (clone $salesQuery)->whereYear('sale_date', now()->year)->sum('total_amount');
    $yearlyPurchases = (clone $purchaseQuery)->whereYear('purchase_date', now()->year)->sum('total_amount');
    $yearlyExpenses = (clone $expenseQuery)->whereYear('expense_date', now()->year)->sum('amount');
    $yearlyNetProfit = $yearlySales - $yearlyPurchases - $yearlyExpenses;

    // ---------------- MONTHLY CHART (FIXED FOR BLADE)
    $monthlySales = [];
    $monthlyExpenses = [];

    for ($m = 1; $m <= 12; $m++) {
        $monthlySales[$m] = (clone $salesQuery)
            ->whereMonth('sale_date', $m)
            ->whereYear('sale_date', now()->year)
            ->sum('total_amount');

        $monthlyExpenses[$m] = (clone $expenseQuery)
            ->whereMonth('expense_date', $m)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');
    }

    return view('dashboard.index', [
        'shopId' => $shopId,

        // Daily
        'dailySales' => $dailySales,
        'dailyPurchases' => $dailyPurchases,
        'dailyExpenses' => $dailyExpenses,
        'dailyNetProfit' => $dailyNetProfit,

        // Weekly
        'weeklySales' => $weeklySales,
        'weeklyPurchases' => $weeklyPurchases,
        'weeklyExpenses' => $weeklyExpenses,
        'weeklyNetProfit' => $weeklyNetProfit,

        // Yearly
        'yearlySales' => $yearlySales,
        'yearlyPurchases' => $yearlyPurchases,
        'yearlyExpenses' => $yearlyExpenses,
        'yearlyNetProfit' => $yearlyNetProfit,

        // Charts
        'monthlySales' => collect($monthlySales),
        'monthlyExpenses' => collect($monthlyExpenses),
    ]);
}
    protected function userDashboard(Request $request)
    {
        $shopId = $request->shop_id;

        $contactData = $this->getContactAnalytics();
        $businessData = $this->getBusinessAnalytics($shopId);

        return view('dashboard.index', array_merge($contactData, $businessData));
    }

    private function getContactAnalytics()
    {
        $totalSubmissions = ContactSubmission::count();

        $submissionsBySubject = ContactSubmission::select('subject', DB::raw('COUNT(*) as count'))
            ->groupBy('subject')
            ->pluck('count', 'subject');

        $days = [];
        $counts = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('M d');

            $counts[] = ContactSubmission::whereDate('created_at', $date)->count();
        }

        return [
            'totalSubmissions' => $totalSubmissions,
            'recentSubmissions' => ContactSubmission::latest()->take(5)->get(),

            'chartDataBySubject' => json_encode([
                'labels' => $submissionsBySubject->keys(),
                'data'   => $submissionsBySubject->values(),
            ]),

            'chartDataTrend' => json_encode([
                'labels' => $days,
                'data'   => $counts,
            ]),
        ];
    }

    private function getBusinessAnalytics($shopId = null)
    {
        $salesQuery = Sale::query();
        $purchaseQuery = Purchase::query();
        $expenseQuery = Expense::query();

        if ($shopId) {
            $salesQuery->where('shop_id', $shopId);
            $purchaseQuery->where('shop_id', $shopId);
            $expenseQuery->where('shop_id', $shopId);
        }

        // Today
        $todaySales = (clone $salesQuery)->whereDate('sale_date', today())->sum('total_amount');
        $todayPurchases = (clone $purchaseQuery)->whereDate('purchase_date', today())->sum('total_amount');
        $todayExpenses = (clone $expenseQuery)->whereDate('expense_date', today())->sum('amount');

        // Month
        $monthSales = (clone $salesQuery)->whereMonth('sale_date', now()->month)->sum('total_amount');
        $monthPurchases = (clone $purchaseQuery)->whereMonth('purchase_date', now()->month)->sum('total_amount');
        $monthExpenses = (clone $expenseQuery)->whereMonth('expense_date', now()->month)->sum('amount');

        // Profit
        $grossProfit = $monthSales - $monthPurchases;
        $netProfit = $monthSales - ($monthPurchases + $monthExpenses);

        // Totals
        $totalSales = $salesQuery->sum('total_amount');
        $totalPurchases = $purchaseQuery->sum('total_amount');
        $totalExpenses = $expenseQuery->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | FIX FOR YOUR ERROR 🔥
        |--------------------------------------------------------------------------
        | We now CREATE $monthlySales & $monthlyExpenses
        | exactly as your Blade expects
        */
        $monthlySales = [];
        $monthlyExpenses = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthlySales[$m] = (clone $salesQuery)
                ->whereMonth('sale_date', $m)
                ->whereYear('sale_date', now()->year)
                ->sum('total_amount');

            $monthlyExpenses[$m] = (clone $expenseQuery)
                ->whereMonth('expense_date', $m)
                ->whereYear('expense_date', now()->year)
                ->sum('amount');
        }

        // Shop performance
        $shopsPerformance = Shop::withSum('sales', 'total_amount')
            ->withSum('purchases', 'total_amount')
            ->withSum('expenses', 'amount')
            ->get();

        return [
            'shopId' => $shopId,

            // Today
            'todaySales' => $todaySales,
            'todayPurchases' => $todayPurchases,
            'todayExpenses' => $todayExpenses,

            // Month
            'monthSales' => $monthSales,
            'monthPurchases' => $monthPurchases,
            'monthExpenses' => $monthExpenses,
            // Profit
            'grossProfit' => $grossProfit,
            'netProfit' => $netProfit,
'dailyNetProfit' => $dailyNetProfit ?? ($todaySales - $todayPurchases - $todayExpenses),
            // Totals
            'totalSales' => $totalSales,
            'totalPurchases' => $totalPurchases,
            'totalExpenses' => $totalExpenses,

            // 🔥 THIS FIXES YOUR BLADE ERROR
            'monthlySales' => collect($monthlySales),
            'monthlyExpenses' => collect($monthlyExpenses),

            'shopsPerformance' => $shopsPerformance,
        ];
    }
}
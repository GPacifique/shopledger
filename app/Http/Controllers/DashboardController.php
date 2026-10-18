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
        'system_admin'   => redirect()->route('system-admin.dashboard'),
        'business_admin' => redirect()->route('business.dashboard'),
        'seller'         => redirect()->route('seller.dashboard'),
        'waiter'         =>redirect()->route('orders.waiter'),
        'accountant'     => redirect()->route('accountant.dashboard'),
        'manager'        =>redirect()->route('seller.dashboard'),
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
   
}
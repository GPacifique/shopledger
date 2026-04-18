<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $shopId = $request->shop_id;

        /*
        |--------------------------------------------------------------------------
        | BASE QUERY FILTERS
        |--------------------------------------------------------------------------
        */
        $salesQuery = Sale::query();
        $purchaseQuery = Purchase::query();
        $expenseQuery = Expense::query();

        if ($shopId) {
            $salesQuery->where('shop_id', $shopId);
            $purchaseQuery->where('shop_id', $shopId);
            $expenseQuery->where('shop_id', $shopId);
        }

        /*
        |--------------------------------------------------------------------------
        | TODAY STATS
        |--------------------------------------------------------------------------
        */
        $todaySales = (clone $salesQuery)
            ->whereDate('sale_date', today())
            ->sum('total_amount');

        $todayPurchases = (clone $purchaseQuery)
            ->whereDate('purchase_date', today())
            ->sum('total_amount');

        $todayExpenses = (clone $expenseQuery)
            ->whereDate('expense_date', today())
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | MONTH STATS
        |--------------------------------------------------------------------------
        */
        $monthSales = (clone $salesQuery)
            ->whereMonth('sale_date', now()->month)
            ->sum('total_amount');

        $monthPurchases = (clone $purchaseQuery)
            ->whereMonth('purchase_date', now()->month)
            ->sum('total_amount');

        $monthExpenses = (clone $expenseQuery)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | PROFIT CALCULATION (REAL BUSINESS LOGIC)
        |--------------------------------------------------------------------------
        */
        $grossProfit = $monthSales - $monthPurchases;
        $netProfit = $monthSales - ($monthPurchases + $monthExpenses);

        /*
        |--------------------------------------------------------------------------
        | TOTALS (ALL TIME)
        |--------------------------------------------------------------------------
        */
        $totalSales = $salesQuery->sum('total_amount');
        $totalPurchases = $purchaseQuery->sum('total_amount');
        $totalExpenses = $expenseQuery->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | MONTHLY CHART DATA (LAST 6 MONTHS)
        |--------------------------------------------------------------------------
        */
        $months = [];
        $salesData = [];
        $purchaseData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);

            $months[] = $month->format('M');

            $salesData[] = (clone $salesQuery)
                ->whereMonth('sale_date', $month->month)
                ->whereYear('sale_date', $month->year)
                ->sum('total_amount');

            $purchaseData[] = (clone $purchaseQuery)
                ->whereMonth('purchase_date', $month->month)
                ->whereYear('purchase_date', $month->year)
                ->sum('total_amount');

            $expenseData[] = (clone $expenseQuery)
                ->whereMonth('expense_date', $month->month)
                ->whereYear('expense_date', $month->year)
                ->sum('amount');
        }

        /*
        |--------------------------------------------------------------------------
        | SHOP PERFORMANCE
        |--------------------------------------------------------------------------
        */
        $shopsPerformance = Shop::withSum('sales', 'total_amount')
            ->withSum('purchases', 'total_amount')
            ->withSum('expenses', 'amount')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */
        return view('dashboard.index', [
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

            // Totals
            'totalSales' => $totalSales,
            'totalPurchases' => $totalPurchases,
            'totalExpenses' => $totalExpenses,

            // Charts
            'chartData' => [
                'labels' => $months,
                'sales' => $salesData,
                'purchases' => $purchaseData,
                'expenses' => $expenseData,
            ],
$months = range(1, 12);

$salesData = [];
$expenseData = [];

foreach ($months as $month) {
    $salesData[] = $monthlySales[$month] ?? 0;
    $expenseData[] = $monthlyExpenses[$month] ?? 0;
}

return view('dashboard.index', compact(
    'salesData',
    'expenseData',
    'totalSales',
    'totalPurchases',
    'totalExpenses',
    'profit',
    'shopsPerformance'
));
            // Shop comparison
            'shopsPerformance' => $shopsPerformance,
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\OtherIncome;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ShopAdminController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $shop = $user->shop;

        if (!$shop) {
            return view('dashboard.user', compact('shop'));
        }

        $shopId = $shop->id;
        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | Base Queries
        |--------------------------------------------------------------------------
        */

        $salesQuery = Sale::where('shop_id', $shopId);

        $purchaseQuery = Purchase::where('shop_id', $shopId);

        $expenseQuery = Expense::where('shop_id', $shopId);

        $otherIncomeQuery = OtherIncome::where('shop_id', $shopId);

        /*
        |--------------------------------------------------------------------------
        | Daily Figures
        |--------------------------------------------------------------------------
        */

        $dailySales = (clone $salesQuery)
            ->whereDate('sale_date', $today)
            ->sum('total_amount');

        $dailyPurchases = (clone $purchaseQuery)
            ->whereDate('purchase_date', $today)
            ->sum('total_amount');

        $dailyExpenses = (clone $expenseQuery)
            ->whereDate('expense_date', $today)
            ->sum('amount');

        $dailyOtherIncome = (clone $otherIncomeQuery)
            ->whereDate('income_date', $today)
            ->sum('amount');

        // Net Profit = Sales + Other Income - Purchases - Expenses
        $dailyNetProfit =
            $dailySales
            + $dailyOtherIncome
            - $dailyPurchases
            - $dailyExpenses;

        /*
        |--------------------------------------------------------------------------
        | Weekly Figures
        |--------------------------------------------------------------------------
        */

        $weekRange = [
            now()->startOfWeek(),
            now()->endOfWeek()
        ];

        $weeklySales = (clone $salesQuery)
            ->whereBetween('sale_date', $weekRange)
            ->sum('total_amount');

        $weeklyPurchases = (clone $purchaseQuery)
            ->whereBetween('purchase_date', $weekRange)
            ->sum('total_amount');

        $weeklyExpenses = (clone $expenseQuery)
            ->whereBetween('expense_date', $weekRange)
            ->sum('amount');

        $weeklyOtherIncome = (clone $otherIncomeQuery)
            ->whereBetween('income_date', $weekRange)
            ->sum('amount');

        $weeklyNetProfit =
            $weeklySales
            + $weeklyOtherIncome
            - $weeklyPurchases
            - $weeklyExpenses;

        /*
        |--------------------------------------------------------------------------
        | Monthly Figures
        |--------------------------------------------------------------------------
        */

        $monthlySales = (clone $salesQuery)
            ->whereMonth('sale_date', $today->month)
            ->whereYear('sale_date', $today->year)
            ->sum('total_amount');

        $monthlyPurchases = (clone $purchaseQuery)
            ->whereMonth('purchase_date', $today->month)
            ->whereYear('purchase_date', $today->year)
            ->sum('total_amount');

        $monthlyExpenses = (clone $expenseQuery)
            ->whereMonth('expense_date', $today->month)
            ->whereYear('expense_date', $today->year)
            ->sum('amount');

        $monthlyOtherIncome = (clone $otherIncomeQuery)
            ->whereMonth('income_date', $today->month)
            ->whereYear('income_date', $today->year)
            ->sum('amount');

        $monthlyNetProfit =
            $monthlySales
            + $monthlyOtherIncome
            - $monthlyPurchases
            - $monthlyExpenses;

        /*
        |--------------------------------------------------------------------------
        | Yearly Figures
        |--------------------------------------------------------------------------
        */

        $yearlySales = (clone $salesQuery)
            ->whereYear('sale_date', $today->year)
            ->sum('total_amount');

        $yearlyPurchases = (clone $purchaseQuery)
            ->whereYear('purchase_date', $today->year)
            ->sum('total_amount');

        $yearlyExpenses = (clone $expenseQuery)
            ->whereYear('expense_date', $today->year)
            ->sum('amount');

        $yearlyOtherIncome = (clone $otherIncomeQuery)
            ->whereYear('income_date', $today->year)
            ->sum('amount');

        $yearlyNetProfit =
            $yearlySales
            + $yearlyOtherIncome
            - $yearlyPurchases
            - $yearlyExpenses;

        /*
        |--------------------------------------------------------------------------
        | Payment Status Breakdown
        |--------------------------------------------------------------------------
        */

        $paidSales = (clone $salesQuery)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $unpaidSales = (clone $salesQuery)
            ->where('payment_status', 'unpaid')
            ->sum('total_amount');

        $partialSales = (clone $salesQuery)
            ->where('payment_status', 'partial')
            ->sum('total_amount');

        $totalSales = $paidSales + $unpaidSales + $partialSales;

        $paymentStatusStats = [
            'paid' => $paidSales,
            'unpaid' => $unpaidSales,
            'partial' => $partialSales,
        ];

        /*
        |--------------------------------------------------------------------------
        | Payment Method Breakdown
        |--------------------------------------------------------------------------
        */

        $paymentMethodStats = [
            'today' => (clone $salesQuery)
                ->whereDate('sale_date', $today)
                ->selectRaw(
                    'payment_method, SUM(total_amount) as total, COUNT(*) as count'
                )
                ->groupBy('payment_method')
                ->pluck('total', 'payment_method')
                ->toArray(),

            'month' => (clone $salesQuery)
                ->whereMonth('sale_date', $today->month)
                ->whereYear('sale_date', $today->year)
                ->selectRaw(
                    'payment_method, SUM(total_amount) as total, COUNT(*) as count'
                )
                ->groupBy('payment_method')
                ->pluck('total', 'payment_method')
                ->toArray(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Shop-Level Counts & Financial Statistics
        |--------------------------------------------------------------------------
        */

        $stats = [

            // Counts
            'total_products' => Product::where('shop_id', $shopId)->count(),

            'total_suppliers' => Supplier::where('shop_id', $shopId)->count(),

            'total_staff' => User::where('shop_id', $shopId)
                ->where('id', '!=', $user->id)
                ->count(),

            'low_stock_products' => Product::where('shop_id', $shopId)
                ->where('stock', '<', 10)
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Daily
            |--------------------------------------------------------------------------
            */

            'dailySales' => $dailySales,
            'dailyOtherIncome' => $dailyOtherIncome,
            'dailyPurchases' => $dailyPurchases,
            'dailyExpenses' => $dailyExpenses,
            'dailyNetProfit' => $dailyNetProfit,

            /*
            |--------------------------------------------------------------------------
            | Weekly
            |--------------------------------------------------------------------------
            */

            'weeklySales' => $weeklySales,
            'weeklyOtherIncome' => $weeklyOtherIncome,
            'weeklyPurchases' => $weeklyPurchases,
            'weeklyExpenses' => $weeklyExpenses,
            'weeklyNetProfit' => $weeklyNetProfit,

            /*
            |--------------------------------------------------------------------------
            | Monthly
            |--------------------------------------------------------------------------
            */

            'monthlySales' => $monthlySales,
            'monthlyOtherIncome' => $monthlyOtherIncome,
            'monthlyPurchases' => $monthlyPurchases,
            'monthlyExpenses' => $monthlyExpenses,
            'monthlyNetProfit' => $monthlyNetProfit,

            /*
            |--------------------------------------------------------------------------
            | Yearly
            |--------------------------------------------------------------------------
            */

            'yearlySales' => $yearlySales,
            'yearlyOtherIncome' => $yearlyOtherIncome,
            'yearlyPurchases' => $yearlyPurchases,
            'yearlyExpenses' => $yearlyExpenses,
            'yearlyNetProfit' => $yearlyNetProfit,

            /*
            |--------------------------------------------------------------------------
            | Payment Statistics
            |--------------------------------------------------------------------------
            */

            'paymentStatusStats' => $paymentStatusStats,
            'paymentMethodStats' => $paymentMethodStats,

            'totalSales' => $totalSales,
            'paidSales' => $paidSales,
            'unpaidSales' => $unpaidSales,
            'partialSales' => $partialSales,

            /*
            |--------------------------------------------------------------------------
            | Dashboard Compatibility
            |--------------------------------------------------------------------------
            */

            'today_sales' => $dailySales,

            'today_purchases' => $dailyPurchases,

            'today_other_income' => $dailyOtherIncome,

            'month_sales' => $monthlySales,

            'month_purchases' => $monthlyPurchases,

            'month_other_income' => $monthlyOtherIncome,

            'month_expenses' => $monthlyExpenses,

            'month_net_profit' => $monthlyNetProfit,
        ];

        /*
        |--------------------------------------------------------------------------
        | Recent Activity
        |--------------------------------------------------------------------------
        */

        $recentSales = Sale::where('shop_id', $shopId)
            ->with('items.product')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $recentPurchases = Purchase::where('shop_id', $shopId)
            ->with(['supplier', 'items.product'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $recentOtherIncomes = OtherIncome::where('shop_id', $shopId)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Stock Movements
        |--------------------------------------------------------------------------
        */

        $recentStockMovements = StockMovement::where('shop_id', $shopId)
            ->with(['product', 'creator'])
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        // Positive quantity_change = stock coming in
        $todayStockIn = StockMovement::where('shop_id', $shopId)
            ->whereDate('created_at', $today)
            ->where('quantity_change', '>', 0)
            ->sum('quantity_change');

        // Negative quantity_change = stock going out
        $todayStockOut = StockMovement::where('shop_id', $shopId)
            ->whereDate('created_at', $today)
            ->where('quantity_change', '<', 0)
            ->sum(DB::raw('ABS(quantity_change)'));

        /*
        |--------------------------------------------------------------------------
        | Product Health
        |--------------------------------------------------------------------------
        */

        $lowStockProducts = Product::where('shop_id', $shopId)
            ->where('stock', '<', 10)
            ->orderBy('stock')
            ->take(5)
            ->get();

        $outOfStockProducts = Product::where('shop_id', $shopId)
            ->where('stock', '<=', 0)
            ->orderBy('name')
            ->take(5)
            ->get();

        $expiringProducts = Product::where('shop_id', $shopId)
            ->whereNotNull('expiry_date')
            ->whereBetween(
                'expiry_date',
                [
                    now()->startOfDay(),
                    now()->addDays(30)->endOfDay()
                ]
            )
            ->orderBy('expiry_date')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Staff
        |--------------------------------------------------------------------------
        */

        $staff = User::where('shop_id', $shopId)
            ->where('id', '!=', $user->id)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Charts
        |--------------------------------------------------------------------------
        */

        $chartData = $this->getChartData($shopId, 7);

        $monthlyChartData = $this->getMonthlyChartData($shopId, 6);

        /*
        |--------------------------------------------------------------------------
        | Sales By Category
        |--------------------------------------------------------------------------
        */

        $salesCategoryData = SaleItem::query()
            ->join(
                'sales',
                'sale_items.sale_id',
                '=',
                'sales.id'
            )
            ->join(
                'products',
                'sale_items.product_id',
                '=',
                'products.id'
            )
            ->join(
                'categories',
                'products.category_id',
                '=',
                'categories.id'
            )
            ->where('sales.shop_id', $shopId)
            ->selectRaw(
                'categories.name as category,
                SUM(sale_items.quantity * sale_items.unit_price) as total'
            )
            ->groupBy(
                'categories.id',
                'categories.name'
            )
            ->orderByDesc('total')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Expense By Category
        |--------------------------------------------------------------------------
        */

        $expenseCategoryData = Expense::query()
            ->join(
                'expense_categories',
                'expenses.category_id',
                '=',
                'expense_categories.id'
            )
            ->where('expenses.shop_id', $shopId)
            ->selectRaw(
                'expense_categories.name as category,
                SUM(expenses.amount) as total'
            )
            ->groupBy(
                'expense_categories.id',
                'expense_categories.name'
            )
            ->orderByDesc('total')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Return Dashboard
        |--------------------------------------------------------------------------
        */

        return view('dashboard.shop-admin', compact(

            'shop',

            'stats',

            'salesCategoryData',
            'expenseCategoryData',

            'paymentMethodStats',

            'recentSales',
            'recentPurchases',
            'recentOtherIncomes',
            'recentStockMovements',

            'todayStockIn',
            'todayStockOut',

            'lowStockProducts',
            'outOfStockProducts',
            'expiringProducts',

            'staff',

            'chartData',
            'monthlyChartData',

            'dailySales',
            'dailyPurchases',
            'dailyExpenses',
            'dailyOtherIncome',
            'dailyNetProfit',

            'weeklyNetProfit',
            'weeklySales',
            'weeklyPurchases',
            'weeklyExpenses',
            'weeklyOtherIncome',

            'monthlyNetProfit',
            'monthlySales',
            'monthlyPurchases',
            'monthlyExpenses',
            'monthlyOtherIncome',

            'yearlyNetProfit',
            'yearlySales',
            'yearlyPurchases',
            'yearlyExpenses',
            'yearlyOtherIncome'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Monthly Chart Data
    |--------------------------------------------------------------------------
    */

    private function getMonthlyChartData($shopId, $months)
    {
        $labels = [];

        $salesData = [];
        $purchasesData = [];
        $expenseData = [];
        $otherIncomeData = [];
        $profitData = [];

        for ($i = $months - 1; $i >= 0; $i--) {

            $date = Carbon::today()->subMonths($i);

            $labels[] = $date->format('M Y');

            $sales = Sale::where('shop_id', $shopId)
                ->whereMonth('sale_date', $date->month)
                ->whereYear('sale_date', $date->year)
                ->sum('total_amount');

            $purchases = Purchase::where('shop_id', $shopId)
                ->whereMonth('purchase_date', $date->month)
                ->whereYear('purchase_date', $date->year)
                ->sum('total_amount');

            $expenses = Expense::where('shop_id', $shopId)
                ->whereMonth('expense_date', $date->month)
                ->whereYear('expense_date', $date->year)
                ->sum('amount');

            $otherIncome = OtherIncome::where('shop_id', $shopId)
                ->whereMonth('income_date', $date->month)
                ->whereYear('income_date', $date->year)
                ->sum('amount');

            $salesData[] = $sales;

            $purchasesData[] = $purchases;

            $expenseData[] = $expenses;

            $otherIncomeData[] = $otherIncome;

            $profitData[] =
                $sales
                + $otherIncome
                - $purchases
                - $expenses;
        }

        return [
            'labels' => $labels,

            'sales' => $salesData,

            'purchases' => $purchasesData,

            'expenses' => $expenseData,

            'other_income' => $otherIncomeData,

            'profit' => $profitData,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Daily Chart Data
    |--------------------------------------------------------------------------
    */

    private function getChartData($shopId, $days = 7)
    {
        $labels = [];

        $salesData = [];
        $purchaseData = [];
        $expenseData = [];
        $otherIncomeData = [];
        $profitData = [];

        for ($i = $days - 1; $i >= 0; $i--) {

            $date = Carbon::today()->subDays($i);

            $labels[] = $date->format('D');

            $sales = Sale::where('shop_id', $shopId)
                ->whereDate('sale_date', $date)
                ->sum('total_amount');

            $purchases = Purchase::where('shop_id', $shopId)
                ->whereDate('purchase_date', $date)
                ->sum('total_amount');

            $expenses = Expense::where('shop_id', $shopId)
                ->whereDate('expense_date', $date)
                ->sum('amount');

            $otherIncome = OtherIncome::where('shop_id', $shopId)
                ->whereDate('income_date', $date)
                ->sum('amount');

            $salesData[] = $sales;

            $purchaseData[] = $purchases;

            $expenseData[] = $expenses;

            $otherIncomeData[] = $otherIncome;

            $profitData[] =
                $sales
                + $otherIncome
                - $purchases
                - $expenses;
        }

        return [

            'labels' => $labels,

            'sales' => $salesData,

            'purchases' => $purchaseData,

            'expenses' => $expenseData,

            'other_income' => $otherIncomeData,

            'dailyNetProfit' => $profitData,
        ];
    }
}

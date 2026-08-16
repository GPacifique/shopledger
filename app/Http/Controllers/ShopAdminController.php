<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\ExpenseCategory;
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

        $salesQuery = Sale::where('shop_id', $shopId);
        $purchaseQuery = Purchase::where('shop_id', $shopId);
        $expenseQuery = Expense::where('shop_id', $shopId);

        // --- Daily figures ---
        $dailySales = (clone $salesQuery)->whereDate('sale_date', $today)->sum('total_amount');
        $dailyPurchases = (clone $purchaseQuery)->whereDate('purchase_date', $today)->sum('total_amount');
        $dailyExpenses = (clone $expenseQuery)->whereDate('expense_date', $today)->sum('amount');
        $dailyNetProfit = $dailySales - $dailyPurchases - $dailyExpenses;

        // --- Weekly figures ---
        $weekRange = [now()->startOfWeek(), now()->endOfWeek()];
        $weeklySales = (clone $salesQuery)->whereBetween('sale_date', $weekRange)->sum('total_amount');
        $weeklyPurchases = (clone $purchaseQuery)->whereBetween('purchase_date', $weekRange)->sum('total_amount');
        $weeklyExpenses = (clone $expenseQuery)->whereBetween('expense_date', $weekRange)->sum('amount');
        $weeklyNetProfit = $weeklySales - $weeklyPurchases - $weeklyExpenses;

        // --- Yearly figures (computed once) ---
        $yearlySales = (clone $salesQuery)->whereYear('sale_date', $today->year)->sum('total_amount');
        $yearlyPurchases = (clone $purchaseQuery)->whereYear('purchase_date', $today->year)->sum('total_amount');
        $yearlyExpenses = (clone $expenseQuery)->whereYear('expense_date', $today->year)->sum('amount');
        $yearlyNetProfit = $yearlySales - $yearlyPurchases - $yearlyExpenses;

        // --- Payment status breakdown (computed once) ---
        $paidSales = (clone $salesQuery)->where('payment_status', 'Paid')->sum('total_amount');
        $unpaidSales = (clone $salesQuery)->where('payment_status', 'Unpaid')->sum('total_amount');
        $partialSales = (clone $salesQuery)->where('payment_status', 'Partial')->sum('total_amount');
        $totalSales = $paidSales + $unpaidSales + $partialSales;

        $paymentStatusStats = [
            'Paid' => $paidSales,
            'Unpaid' => $unpaidSales,
            'Partial' => $partialSales,
        ];

        // --- Payment method breakdown: today vs this month ---
        $paymentMethodStats = [
            'today' => (clone $salesQuery)
                ->whereDate('sale_date', $today)
                ->selectRaw('payment_method, SUM(total_amount) as total, COUNT(*) as count')
                ->groupBy('payment_method')
                ->pluck('total', 'payment_method')
                ->toArray(),
            'month' => (clone $salesQuery)
                ->whereMonth('sale_date', $today->month)
                ->whereYear('sale_date', $today->year)
                ->selectRaw('payment_method, SUM(total_amount) as total, COUNT(*) as count')
                ->groupBy('payment_method')
                ->pluck('total', 'payment_method')
                ->toArray(),
        ];

        // --- Shop-level counts ---
        $stats = [
            'total_products' => Product::where('shop_id', $shopId)->count(),
            'total_suppliers' => Supplier::where('shop_id', $shopId)->count(),
            'total_staff' => User::where('shop_id', $shopId)->where('id', '!=', $user->id)->count(),
            'low_stock_products' => Product::where('shop_id', $shopId)->where('stock', '<', 10)->count(),

            'dailySales' => $dailySales,
            'dailyPurchases' => $dailyPurchases,
            'dailyExpenses' => $dailyExpenses,
            'dailyNetProfit' => $dailyNetProfit,

            'weeklySales' => $weeklySales,
            'weeklyPurchases' => $weeklyPurchases,
            'weeklyExpenses' => $weeklyExpenses,
            'weeklyNetProfit' => $weeklyNetProfit,

            'yearlySales' => $yearlySales,
            'yearlyPurchases' => $yearlyPurchases,
            'yearlyExpenses' => $yearlyExpenses,
            'yearlyNetProfit' => $yearlyNetProfit,

            'paymentStatusStats' => $paymentStatusStats,
            'paymentMethodStats' => $paymentMethodStats,

            'totalSales' => $totalSales,
            'paidSales' => $paidSales,
            'unpaidSales' => $unpaidSales,
            'partialSales' => $partialSales,

            'today_sales' => $dailySales,
            'today_purchases' => $dailyPurchases,

            'month_sales' => (clone $salesQuery)
                ->whereMonth('sale_date', $today->month)
                ->whereYear('sale_date', $today->year)
                ->sum('total_amount'),
            'month_purchases' => (clone $purchaseQuery)
                ->whereMonth('purchase_date', $today->month)
                ->whereYear('purchase_date', $today->year)
                ->sum('total_amount'),
        ];

        // --- Recent activity ---
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

        // --- Stock movements (shop-wide, all staff) ---
        $recentStockMovements = StockMovement::where('shop_id', $shopId)
            ->with(['product', 'creator'])
            ->orderByDesc('created_at')
            ->take(10)
            ->get();
// --- Today's stock movements ---
// quantity_change is:
//   positive = stock coming in
//   negative = stock going out

$todayStockIn = StockMovement::where('shop_id', $shopId)
    ->whereDate('created_at', $today)
    ->where('quantity_change', '>', 0)
    ->sum('quantity_change');

$todayStockOut = StockMovement::where('shop_id', $shopId)
    ->whereDate('created_at', $today)
    ->where('quantity_change', '<', 0)
    ->sum(DB::raw('ABS(quantity_change)'));

        // --- Product health ---
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
            ->whereBetween('expiry_date', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
            ->orderBy('expiry_date')
            ->take(5)
            ->get();

        // --- Staff ---
        $staff = User::where('shop_id', $shopId)
            ->where('id', '!=', $user->id)
            ->get();

        // --- Charts ---
        $chartData = $this->getChartData($shopId, 7);
        $monthlyChartData = $this->getMonthlyChartData($shopId, 6);

        $salesCategoryData = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('sales.shop_id', $shopId)
            ->selectRaw('categories.name as category, SUM(sale_items.quantity * sale_items.unit_price) as total')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->get();

        $expenseCategoryData = Expense::query()
            ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->where('expenses.shop_id', $shopId)
            ->selectRaw('expense_categories.name as category, SUM(expenses.amount) as total')
            ->groupBy('expense_categories.id', 'expense_categories.name')
            ->orderByDesc('total')
            ->get();

        return view('dashboard.shop-admin', compact(
            'shop',
            'stats',
            'salesCategoryData',
            'expenseCategoryData',
            'paymentMethodStats',
            'recentSales',
            'recentPurchases',
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
            'dailyNetProfit',
            'weeklyNetProfit',
            'weeklySales',
            'weeklyPurchases',
            'weeklyExpenses',
            'yearlyNetProfit',
            'yearlySales',
            'yearlyPurchases',
            'yearlyExpenses'
        ));
    }

    private function getMonthlyChartData($shopId, $months)
    {
        $labels = [];
        $salesData = [];
        $purchasesData = [];
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

            $salesData[] = $sales;
            $purchasesData[] = $purchases;
            $profitData[] = $sales - $purchases;
        }

        return [
            'labels' => $labels,
            'sales' => $salesData,
            'purchases' => $purchasesData,
            'profit' => $profitData,
        ];
    }

    private function getChartData($shopId, $days = 7)
    {
        $labels = [];
        $salesData = [];
        $purchaseData = [];
        $expenseData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('D');

            $salesData[] = Sale::where('shop_id', $shopId)
                ->whereDate('sale_date', $date)
                ->sum('total_amount');

            $purchaseData[] = Purchase::where('shop_id', $shopId)
                ->whereDate('purchase_date', $date)
                ->sum('total_amount');

            $expenseData[] = Expense::where('shop_id', $shopId)
                ->whereDate('expense_date', $date)
                ->sum('amount');
        }

        // Fixed: $dailyNetProfit doesn't exist in this method's scope
        // (it belongs to dashboard(), a different method) — compute the
        // daily profit series directly instead of referencing a variable
        // that was never in reach here.
        $profitData = array_map(function ($s, $p, $e) {
            return $s - $p - $e;
        }, $salesData, $purchaseData, $expenseData);

        return [
            'labels' => $labels,
            'sales' => $salesData,
            'purchases' => $purchaseData,
            'expenses' => $expenseData,
            'dailyNetProfit' => $profitData,
        ];
    }
}
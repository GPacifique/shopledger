<?php
namespace App\Http\Controllers;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\SaleItem;
use App\Models\ExpenseCategory;
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
          $salesQuery = Sale::where('shop_id', $shopId);
    $purchaseQuery = Purchase::where('shop_id', $shopId);
    $expenseQuery = Expense::where('shop_id', $shopId);
  $dailySales = (clone $salesQuery)->whereDate('sale_date', today())->sum('total_amount');
    $dailyPurchases = (clone $purchaseQuery)->whereDate('purchase_date', today())->sum('total_amount');
    $dailyExpenses = (clone $expenseQuery)->whereDate('expense_date', today())->sum('amount');
    $dailyNetProfit = $dailySales - $dailyPurchases - $dailyExpenses;
    $yearlyNetProfit = (clone $salesQuery)->whereYear('sale_date', now()->year)->sum('total_amount') - (clone $purchaseQuery)->whereYear('purchase_date', now()->year)->sum('total_amount') - (clone $expenseQuery)->whereYear('expense_date', now()->year)->sum('amount');
    $weeklySales = (clone $salesQuery)->whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount');
    $weeklyPurchases = (clone $purchaseQuery)->whereBetween('purchase_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount');
    $weeklyExpenses = (clone $expenseQuery)->whereBetween('expense_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount');
$weeklyNetProfit = (clone $salesQuery)->whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount') - (clone $purchaseQuery)->whereBetween('purchase_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount') - (clone $expenseQuery)->whereBetween('expense_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount');   
$yearlySales = (clone $salesQuery)->whereYear('sale_date', now()->year)->sum('total_amount');
    $yearlyPurchases = (clone $purchaseQuery)->whereYear('purchase_date', now()->year)->sum('total_amount');
    $yearlyExpenses = (clone $expenseQuery)->whereYear('expense_date', now()->year)->sum('amount');
     $yearlyNetProfit = $yearlySales - $yearlyPurchases - $yearlyExpenses;  
     $paidSales = (clone $salesQuery)->where('payment_status', 'paid')->sum('total_amount');
    $unpaidSales = (clone $salesQuery)->where('payment_status', 'unpaid')->sum('total_amount');
    $partialSales = (clone $salesQuery)->where('payment_status', 'partial')->sum('total_amount');   
    $totalSales = $paidSales + $unpaidSales + $partialSales;
     $paymentMethodStats = (clone $salesQuery)
        ->selectRaw('payment_method, SUM(total_amount) as total, COUNT(*) as count')
        ->groupBy('payment_method')
        ->pluck('total', 'payment_method')
        ->toArray();  
        $totalunpaidSales = (clone $salesQuery)->where('payment_status', 'unpaid')->sum('total_amount');
        $totalpartialSales = (clone $salesQuery)->where('payment_status', 'partial')->sum('total_amount');
        $totalpaidSales = (clone $salesQuery)->where('payment_status', 'paid')->sum('total_amount');
         $totalSales = $totalunpaidSales + $totalpartialSales + $totalpaidSales;
         $paymentStatusStats = [
        'paid' => $totalpaidSales,
        'unpaid' => $totalunpaidSales,
        'partial' => $totalpartialSales
    ];

         $paymentMethodStats = (clone $salesQuery)
        ->selectRaw('payment_method, SUM(total_amount) as total, COUNT(*) as count')
        ->groupBy('payment_method')
        ->pluck('total', 'payment_method')
        ->toArray();            
if (!$shop) {
            return view('dashboard.user', compact('shop'));
        }

        // Get shop statistics
        $stats =[      
            'total_products'=> Product::where('shop_id', $shop->id)->count(),
            'total_suppliers' => Supplier::where('shop_id', $shop->id)->count(),
            'total_staff' => User::where('shop_id', $shop->id)->where('id', '!=', $user->id)->count(),
            'low_stock_products' => Product::where('shop_id', $shop->id)->where('stock', '<', 10)->count(),
             'dailySales' => $dailySales,
        'dailyPurchases' => $dailyPurchases,
        'dailyExpenses' => $dailyExpenses,
        'dailyNetProfit' => $dailyNetProfit,
        'weeklyNetProfit' => $weeklyNetProfit,
        'weeklySales' => $weeklySales,
        'weeklyPurchases' => $weeklyPurchases,
        'weeklyExpenses' => $weeklyExpenses,
        'yearlyNetProfit' => $yearlyNetProfit,
        'yearlySales' => $yearlySales,
        'yearlyPurchases' => $yearlyPurchases,
        'yearlyExpenses' => $yearlyExpenses,
         'paymentStatusStats' => $paymentStatusStats,
         'paymentMethodStats' => $paymentMethodStats,
         'totalSales' => $totalSales,
         'paidSales' => $paidSales,
         'unpaidSales' => $unpaidSales,
         'partialSales' => $partialSales,
        ];
        // Today's stats
        $today =Carbon::today();
        $stats['today_sales'] = Sale::where('shop_id', $shop->id)
            ->whereDate('sale_date', $today)
            ->sum('total_amount');
        $stats['today_purchases'] = Purchase::where('shop_id', $shop->id)
            ->whereDate('purchase_date', $today)
            ->sum('total_amount');

        // This month stats
        $stats['month_sales'] = Sale::where('shop_id', $shop->id)
            ->whereMonth('sale_date', $today->month)
            ->whereYear('sale_date', $today->year)
            ->sum('total_amount');
        $stats['month_purchases'] = Purchase::where('shop_id', $shop->id)
            ->whereMonth('purchase_date', $today->month)
            ->whereYear('purchase_date', $today->year)
            ->sum('total_amount');

        // Payment method stats - Today
        $paymentMethodStats = [
            'today' => Sale::where('shop_id', $shop->id)
                ->whereDate('sale_date', $today)
                ->selectRaw('payment_method, SUM(total_amount) as total, COUNT(*) as count')
                ->groupBy('payment_method')
                ->pluck('total', 'payment_method')
                ->toArray(),
            'month' => Sale::where('shop_id', $shop->id)
                ->whereMonth('sale_date', $today->month)
                ->whereYear('sale_date', $today->year)
                ->selectRaw('payment_method, SUM(total_amount) as total, COUNT(*) as count')
                ->groupBy('payment_method')
                ->pluck('total', 'payment_method')
                ->toArray(),
        ];

        // Recent sales
        $recentSales = Sale::where('shop_id', $shop->id)
            ->with('items.product')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // Recent purchases
        $recentPurchases = Purchase::where('shop_id', $shop->id)
            ->with(['supplier', 'items.product'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // Low stock products
        $lowStockProducts = Product::where('shop_id', $shop->id)
            ->where('stock', '<', 10)
            ->orderBy('stock')
            ->take(5)
            ->get();

        // Staff members
        $staff = User::where('shop_id', $shop->id)
            ->where('id', '!=', $user->id)
            ->get();

        // Chart data - Last 7 days sales vs purchases
        $chartData = $this->getChartData($shop->id, 7);

        // Chart data - Last 6 months
        $monthlyChartData = $this->getMonthlyChartData($shop->id, 6);
// Sales by Product Category
$salesCategoryData = SaleItem::query()
    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
    ->join('products', 'sale_items.product_id', '=', 'products.id')
    ->join('categories', 'products.category_id', '=', 'categories.id')
    ->where('sales.shop_id', $shopId)
    ->selectRaw('categories.name as category, SUM(sale_items.quantity * sale_items.unit_price) as total')
    ->groupBy('categories.id', 'categories.name')
    ->orderByDesc('total')
    ->get();


// Expenses by Expense Category
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
    'lowStockProducts',
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

    return [
        'labels' => $labels,
        'sales' => $salesData,
        'purchases' => $purchaseData,
        'expenses' => $expenseData,
        'dailyNetProfit' => $dailyNetProfit ?? array_map(function($s, $p, $e) {
            return $s - $p - $e;
        }, $salesData, $purchaseData, $expenseData),
    ];
}
}

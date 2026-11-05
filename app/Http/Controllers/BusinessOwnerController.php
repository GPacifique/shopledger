<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Shop;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\OtherIncome;
use App\Models\Product;
use App\Models\Customer;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BusinessOwnerController extends Controller
{
    public function dashboard(Request $request): View
    {
        $shops = $request->user()
            ->shops()
            ->with('subscription')
            ->withCount('products')
            ->orderBy('business_name')
            ->get();
        $shopIds = $shops->modelKeys();
        $periods = ['today', 'yesterday', 'this_week', 'this_month', 'last_month', 'this_year', 'custom'];
        $shopChoices = array_merge(['all'], array_map('strval', $shopIds));
        $filters = $request->validate([
            'shop' => ['nullable', Rule::in($shopChoices)],
            'period' => ['nullable', Rule::in($periods)],
            'start_date' => ['required_if:period,custom', 'nullable', 'date_format:Y-m-d'],
            'end_date' => ['required_if:period,custom', 'nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);

        $period = $filters['period'] ?? 'this_month';
        $selectedShopId = ($filters['shop'] ?? 'all') === 'all'
            ? null
            : (int) $filters['shop'];
        $selectedShopIds = $selectedShopId === null ? $shopIds : [$selectedShopId];
        $selectedShop = $selectedShopId === null ? null : $shops->firstWhere('id', $selectedShopId);

        $today = today();
        [$startDate, $endDate] = match ($period) {
            'today' => [$today->copy(), $today->copy()],
            'yesterday' => [$today->copy()->subDay(), $today->copy()->subDay()],
            'this_week' => [now()->startOfWeek(), now()->endOfWeek()],
            'last_month' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()],
            'this_year' => [now()->startOfYear(), now()->endOfYear()],
            'custom' => [Carbon::parse($filters['start_date'])->startOfDay(), Carbon::parse($filters['end_date'])->endOfDay()],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
        $startDate = $startDate->copy()->startOfDay();
        $endDate = $endDate->copy()->endOfDay();
        $dayCount = (int) $startDate->diffInDays($endDate) + 1;
        if ($dayCount > 1827) {
            throw ValidationException::withMessages([
                'end_date' => __('Choose a date range of five years or less.'),
            ]);
        }

        $within = static fn ($query, string $column, Carbon $start, Carbon $end) => $query
            ->whereDate($column, '>=', $start->toDateString())
            ->whereDate($column, '<=', $end->toDateString());
        $scoped = static fn ($query) => $query->whereIn(
            $query->getModel()->qualifyColumn('shop_id'),
            $selectedShopIds
        );

        $sales = $within($scoped(Sale::query()), 'sale_date', $startDate, $endDate);
        $purchases = $within($scoped(Purchase::query()), 'purchase_date', $startDate, $endDate);
        $expenses = $within($scoped(Expense::query()), 'expense_date', $startDate, $endDate);
        $otherIncome = $within($scoped(OtherIncome::query()), 'income_date', $startDate, $endDate);

        $salesTotal = (float) (clone $sales)->sum('total_amount');
        $transactionCount = (int) (clone $sales)->count();
        $purchaseTotal = (float) (clone $purchases)->sum('total_amount');
        $expenseTotal = (float) (clone $expenses)->sum('amount');
        $otherIncomeTotal = (float) (clone $otherIncome)->sum('amount');
        $costOfGoodsSold = (float) SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereIn('sale_items.shop_id', $selectedShopIds)
            ->whereDate('sales.sale_date', '>=', $startDate->toDateString())
            ->whereDate('sales.sale_date', '<=', $endDate->toDateString())
            ->sum(DB::raw('sale_items.quantity * sale_items.cost_price_at_sale'));
        $grossProfit = $salesTotal - $costOfGoodsSold;
        $netProfit = $grossProfit - $expenseTotal + $otherIncomeTotal;
        $previousStart = $startDate->copy()->subDays($dayCount)->startOfDay();
        $previousEnd = $startDate->copy()->subDay()->endOfDay();
        $previousSales = (float) $within($scoped(Sale::query()), 'sale_date', $previousStart, $previousEnd)
            ->sum('total_amount');
        $salesGrowth = $previousSales > 0
            ? round((($salesTotal - $previousSales) / $previousSales) * 100, 1)
            : ($salesTotal > 0 ? null : 0.0);

        $periodSales = fn (Carbon $start, Carbon $end) => (float) $within(
            $scoped(Sale::query()),
            'sale_date',
            $start->copy()->startOfDay(),
            $end->copy()->endOfDay()
        )->sum('total_amount');
        $salesByShopRows = (clone $sales)
            ->selectRaw('shop_id, SUM(total_amount) as total, COUNT(*) as transactions')
            ->groupBy('shop_id')
            ->get()
            ->keyBy('shop_id');
        $purchasesByShop = (clone $purchases)
            ->selectRaw('shop_id, SUM(total_amount) as total')
            ->groupBy('shop_id')
            ->pluck('total', 'shop_id');
        $expensesByShop = (clone $expenses)
            ->selectRaw('shop_id, SUM(amount) as total')
            ->groupBy('shop_id')
            ->pluck('total', 'shop_id');
        $incomeByShop = (clone $otherIncome)
            ->selectRaw('shop_id, SUM(amount) as total')
            ->groupBy('shop_id')
            ->pluck('total', 'shop_id');
        $cogsByShop = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereIn('sale_items.shop_id', $selectedShopIds)
            ->whereDate('sales.sale_date', '>=', $startDate->toDateString())
            ->whereDate('sales.sale_date', '<=', $endDate->toDateString())
            ->selectRaw('sale_items.shop_id, SUM(sale_items.quantity * sale_items.cost_price_at_sale) as total')
            ->groupBy('sale_items.shop_id')
            ->pluck('total', 'sale_items.shop_id');
        $salesTodayByShop = Sale::query()
            ->whereIn('shop_id', $selectedShopIds)
            ->whereDate('sale_date', today())
            ->selectRaw('shop_id, SUM(total_amount) as total')
            ->groupBy('shop_id')
            ->pluck('total', 'shop_id');
        $salesMonthByShop = $within(
            Sale::query()->whereIn('shop_id', $selectedShopIds),
            'sale_date',
            now()->startOfMonth(),
            now()->endOfMonth()
        )
            ->selectRaw('shop_id, SUM(total_amount) as total')
            ->groupBy('shop_id')
            ->pluck('total', 'shop_id');
        $expensesMonthByShop = $within(
            Expense::query()->whereIn('shop_id', $selectedShopIds),
            'expense_date',
            now()->startOfMonth(),
            now()->endOfMonth()
        )
            ->selectRaw('shop_id, SUM(amount) as total')
            ->groupBy('shop_id')
            ->pluck('total', 'shop_id');
        $incomeMonthByShop = $within(
            OtherIncome::query()->whereIn('shop_id', $selectedShopIds),
            'income_date',
            now()->startOfMonth(),
            now()->endOfMonth()
        )
            ->selectRaw('shop_id, SUM(amount) as total')
            ->groupBy('shop_id')
            ->pluck('total', 'shop_id');
        $cogsMonthByShop = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereIn('sale_items.shop_id', $selectedShopIds)
            ->whereDate('sales.sale_date', '>=', now()->startOfMonth()->toDateString())
            ->whereDate('sales.sale_date', '<=', now()->endOfMonth()->toDateString())
            ->selectRaw('sale_items.shop_id, SUM(sale_items.quantity * sale_items.cost_price_at_sale) as total')
            ->groupBy('sale_items.shop_id')
            ->pluck('total', 'sale_items.shop_id');
        $employeeCounts = User::query()
            ->whereIn('shop_id', $selectedShopIds)
            ->whereNotIn('role', ['business_owner', 'system_admin'])
            ->selectRaw('shop_id, COUNT(*) as total')
            ->groupBy('shop_id')
            ->pluck('total', 'shop_id');
        $lowStockCounts = Product::query()
            ->whereIn('shop_id', $selectedShopIds)
            ->where('stock', '>', 0)
            ->whereRaw('stock <= minimum_stock')
            ->selectRaw('shop_id, COUNT(*) as total')
            ->groupBy('shop_id')
            ->pluck('total', 'shop_id');
        $shopPerformance = $shops
            ->whereIn('id', $selectedShopIds)
            ->map(function (Shop $shop) use ($salesByShopRows, $purchasesByShop, $expensesByShop, $incomeByShop, $cogsByShop, $salesTodayByShop, $salesMonthByShop, $expensesMonthByShop, $incomeMonthByShop, $cogsMonthByShop, $employeeCounts, $lowStockCounts): array {
                $shopSales = (float) ($salesByShopRows->get($shop->id)?->total ?? 0);
                $shopExpenses = (float) $expensesByShop->get($shop->id, 0);

                return [
                    'shop' => $shop,
                    'sales' => $shopSales,
                    'transactions' => (int) ($salesByShopRows->get($shop->id)?->transactions ?? 0),
                    'purchases' => (float) $purchasesByShop->get($shop->id, 0),
                    'expenses' => $shopExpenses,
                    'profit' => $shopSales - (float) $cogsByShop->get($shop->id, 0) - $shopExpenses + (float) $incomeByShop->get($shop->id, 0),
                    'sales_today' => (float) $salesTodayByShop->get($shop->id, 0),
                    'sales_month' => (float) $salesMonthByShop->get($shop->id, 0),
                    'expenses_month' => (float) $expensesMonthByShop->get($shop->id, 0),
                    'profit_month' => (float) $salesMonthByShop->get($shop->id, 0)
                        - (float) $cogsMonthByShop->get($shop->id, 0)
                        - (float) $expensesMonthByShop->get($shop->id, 0)
                        + (float) $incomeMonthByShop->get($shop->id, 0),
                    'products' => (int) $shop->products_count,
                    'employees' => (int) $employeeCounts->get($shop->id, 0),
                    'low_stock' => (int) $lowStockCounts->get($shop->id, 0),
                ];
            })->values();

        $productsQuery = Product::query()->whereIn('shop_id', $selectedShopIds);
        $productCount = (int) (clone $productsQuery)->count();
        $stockQuantity = (float) (clone $productsQuery)->sum('stock');
        $inventoryValue = (float) (clone $productsQuery)->selectRaw('SUM(stock * buying_price) as total')->value('total');
        $lowStockProducts = (clone $productsQuery)
            ->with('shop:id,business_name')
            ->where('stock', '>', 0)
            ->whereRaw('stock <= minimum_stock')
            ->orderBy('stock')
            ->limit(12)
            ->get(['id', 'shop_id', 'name', 'stock', 'minimum_stock']);
        $lowStockTotal = (int) (clone $productsQuery)->where('stock', '>', 0)->whereRaw('stock <= minimum_stock')->count();
        $outOfStockTotal = (int) (clone $productsQuery)->where('stock', '<=', 0)->count();
        $inventoryByShop = (clone $productsQuery)
            ->selectRaw('shop_id, SUM(stock * buying_price) as value, SUM(stock) as quantity')
            ->groupBy('shop_id')
            ->get()
            ->keyBy('shop_id');

        $customersQuery = Customer::query()->whereIn('shop_id', $selectedShopIds);
        $customerCount = (int) (clone $customersQuery)->count();
        $newCustomers = (int) (clone $customersQuery)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $returningCustomers = (int) Sale::query()
            ->whereIn('shop_id', $selectedShopIds)
            ->whereDate('sale_date', '>=', $startDate->toDateString())
            ->whereDate('sale_date', '<=', $endDate->toDateString())
            ->whereNotNull('customer_id')
            ->select('customer_id')
            ->groupBy('customer_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();
        $topCustomers = Sale::query()
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->whereIn('sales.shop_id', $selectedShopIds)
            ->whereDate('sales.sale_date', '>=', $startDate->toDateString())
            ->whereDate('sales.sale_date', '<=', $endDate->toDateString())
            ->selectRaw('customers.id, customers.name, SUM(sales.total_amount) as total, COUNT(sales.id) as transactions')
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
        $totalEmployees = (int) User::query()
            ->whereIn('shop_id', $selectedShopIds)
            ->whereNotIn('role', ['business_owner', 'system_admin'])
            ->count();

        $unpaidSales = (float) (clone $sales)
            ->whereRaw('LOWER(payment_status) = ?', ['unpaid'])
            ->sum('total_amount');
        $unpaidExpenses = (float) (clone $expenses)
            ->whereRaw('LOWER(status) = ?', ['unpaid'])
            ->sum('amount');

        $salesByPaymentMethod = (clone $sales)
            ->selectRaw('payment_method, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();
        $salesByCashier = (clone $sales)
            ->leftJoin('users', 'sales.created_by', '=', 'users.id')
            ->selectRaw("COALESCE(users.name, 'Unknown') as cashier, SUM(sales.total_amount) as total, COUNT(sales.id) as transactions")
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
        $topProducts = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereIn('sale_items.shop_id', $selectedShopIds)
            ->whereDate('sales.sale_date', '>=', $startDate->toDateString())
            ->whereDate('sales.sale_date', '<=', $endDate->toDateString())
            ->selectRaw('products.id, products.name, SUM(sale_items.quantity) as quantity, SUM(sale_items.line_total) as revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('quantity')
            ->limit(8)
            ->get();
        $slowMovingProducts = (clone $productsQuery)
            ->with('shop:id,business_name')
            ->where('stock', '>', 0)
            ->whereDoesntHave('saleItems', fn ($query) => $query->whereHas('sale', fn ($saleQuery) => $saleQuery
                ->whereDate('sale_date', '>=', $startDate->toDateString())
                ->whereDate('sale_date', '<=', $endDate->toDateString())))
            ->orderByDesc('stock')
            ->limit(8)
            ->get(['id', 'shop_id', 'name', 'stock']);

        $dailySalesRows = (clone $sales)
            ->selectRaw('sale_date as day, SUM(total_amount) as total, COUNT(*) as transactions')
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get()
            ->keyBy(fn ($row) => Carbon::parse($row->day)->format('Y-m-d'));
        $dailyPurchaseRows = (clone $purchases)->selectRaw('purchase_date as day, SUM(total_amount) as total')->groupBy('purchase_date')->get()->keyBy(fn ($row) => Carbon::parse($row->day)->format('Y-m-d'));
        $dailyExpenseRows = (clone $expenses)->selectRaw('expense_date as day, SUM(amount) as total')->groupBy('expense_date')->get()->keyBy(fn ($row) => Carbon::parse($row->day)->format('Y-m-d'));
        $dailyIncomeRows = (clone $otherIncome)->selectRaw('income_date as day, SUM(amount) as total')->groupBy('income_date')->get()->keyBy(fn ($row) => Carbon::parse($row->day)->format('Y-m-d'));
        $dailyCogsRows = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereIn('sale_items.shop_id', $selectedShopIds)
            ->whereDate('sales.sale_date', '>=', $startDate->toDateString())
            ->whereDate('sales.sale_date', '<=', $endDate->toDateString())
            ->selectRaw('sales.sale_date as day, SUM(sale_items.quantity * sale_items.cost_price_at_sale) as total')
            ->groupBy('sales.sale_date')
            ->get()
            ->keyBy(fn ($row) => Carbon::parse($row->day)->format('Y-m-d'));
        $dailyMovementRows = StockMovement::query()
            ->whereIn('shop_id', $selectedShopIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as day, SUM(quantity) as quantity')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->keyBy(fn ($row) => Carbon::parse($row->day)->format('Y-m-d'));

        $chartDaily = [];
        foreach (CarbonPeriod::create($startDate->toDateString(), $endDate->toDateString()) as $date) {
            $key = $date->format('Y-m-d');
            $dailySales = (float) ($dailySalesRows->get($key)?->total ?? 0);
            $dailyCost = (float) ($dailyCogsRows->get($key)?->total ?? 0);
            $dailyExpenses = (float) ($dailyExpenseRows->get($key)?->total ?? 0);
            $dailyOtherIncome = (float) ($dailyIncomeRows->get($key)?->total ?? 0);
            $chartDaily[] = [
                'date' => $key,
                'sales' => $dailySales,
                'purchases' => (float) ($dailyPurchaseRows->get($key)?->total ?? 0),
                'expenses' => $dailyExpenses,
                'income' => $dailyOtherIncome,
                'profit' => $dailySales - $dailyCost - $dailyExpenses + $dailyOtherIncome,
                'transactions' => (int) ($dailySalesRows->get($key)?->transactions ?? 0),
                'stock' => (float) ($dailyMovementRows->get($key)?->quantity ?? 0),
            ];
        }
        $chartRows = collect($chartDaily);
        if ($dayCount > 90) {
            $chartRows = $chartRows->groupBy(fn ($row) => substr($row['date'], 0, 7))
                ->map(fn ($rows, $month) => [
                    'date' => $month,
                    'sales' => $rows->sum('sales'),
                    'purchases' => $rows->sum('purchases'),
                    'expenses' => $rows->sum('expenses'),
                    'income' => $rows->sum('income'),
                    'profit' => $rows->sum('profit'),
                    'transactions' => $rows->sum('transactions'),
                    'stock' => $rows->sum('stock'),
                ])->values();
        }

        $recentSales = (clone $sales)->with(['shop:id,business_name', 'creator:id,name'])->latest()->limit(8)->get();
        $recentPurchases = (clone $purchases)->with(['shop:id,business_name', 'creator:id,name', 'supplier:id,name'])->latest()->limit(6)->get();
        $recentExpenses = (clone $expenses)->with(['shop:id,business_name', 'creator:id,name', 'category:id,name'])->latest()->limit(6)->get();
        $recentMovements = StockMovement::query()
            ->whereIn('shop_id', $selectedShopIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['shop:id,business_name', 'creator:id,name', 'product:id,name'])
            ->latest()
            ->limit(8)
            ->get();
        $recentCustomers = (clone $customersQuery)
            ->with('shop:id,business_name')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->limit(5)
            ->get();
        $activities = collect()
            ->concat($recentSales->map(fn (Sale $sale) => [
                'type' => 'Sale',
                'description' => __('Sale') . ' · ' . number_format((float) $sale->total_amount, 2),
                'shop' => $sale->shop?->business_name,
                'user' => $sale->creator?->name ?? __('Unknown'),
                'date' => $sale->created_at,
                'tone' => 'indigo',
            ]))
            ->concat($recentPurchases->map(fn (Purchase $purchase) => [
                'type' => 'Purchase',
                'description' => __('Purchase') . ' · ' . number_format((float) $purchase->total_amount, 2),
                'shop' => $purchase->shop?->business_name,
                'user' => $purchase->creator?->name ?? __('Unknown'),
                'date' => $purchase->created_at,
                'tone' => 'blue',
            ]))
            ->concat($recentExpenses->map(fn (Expense $expense) => [
                'type' => __('Expense'),
                'description' => ($expense->category?->name ?? __('Expense')) . ' · ' . number_format((float) $expense->amount, 2),
                'shop' => $expense->shop?->business_name,
                'user' => $expense->creator?->name ?? __('Unknown'),
                'date' => $expense->created_at,
                'tone' => 'rose',
            ]))
            ->concat($recentMovements->map(fn (StockMovement $movement) => [
                'type' => $movement->type === 'return' ? __('Return') : __('Stock movement'),
                'description' => ($movement->product?->name ?? __('Product')) . ' · ' . $movement->getTypeLabelAttribute() . ' (' . $movement->quantity . ')',
                'shop' => $movement->shop?->business_name,
                'user' => $movement->creator?->name ?? __('Unknown'),
                'date' => $movement->created_at,
                'tone' => $movement->type === 'return' ? 'green' : 'amber',
            ]))
            ->concat($recentCustomers->map(fn (Customer $customer) => [
                'type' => __('New customer'),
                'description' => $customer->name,
                'shop' => $customer->shop?->business_name,
                'user' => __('Customer record'),
                'date' => $customer->created_at,
                'tone' => 'violet',
            ]))
            ->sortByDesc('date')
            ->take(16)
            ->values();

        $salesToday = $periodSales($today, $today);
        $salesThisWeek = $periodSales(now()->startOfWeek(), now()->endOfWeek());
        $salesThisMonth = $periodSales(now()->startOfMonth(), now()->endOfMonth());
        $salesThisYear = $periodSales(now()->startOfYear(), now()->endOfYear());
        $purchasesThisMonth = (float) $within(
            $scoped(Purchase::query()),
            'purchase_date',
            now()->startOfMonth(),
            now()->endOfMonth()
        )->sum('total_amount');
        $averageTransaction = $transactionCount > 0 ? $salesTotal / $transactionCount : 0;
        $supplierCount = (int) Supplier::query()->whereIn('shop_id', $selectedShopIds)->count();
        $purchasesBySupplier = (clone $purchases)
            ->leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->selectRaw("COALESCE(suppliers.name, 'Unknown') as supplier, SUM(purchases.total_amount) as total")
            ->groupBy('suppliers.id', 'suppliers.name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
        $expenseCategories = (clone $expenses)
            ->leftJoin('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->selectRaw("COALESCE(expense_categories.name, 'Uncategorized') as category, SUM(expenses.amount) as total")
            ->groupBy('expense_categories.name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        return view('dashboard.business-owner', [
            'shops' => $shops,
            'selectedShop' => $selectedShop,
            'selectedShopId' => $selectedShopId,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'salesTotal' => $salesTotal,
            'salesToday' => $salesToday,
            'salesThisWeek' => $salesThisWeek,
            'salesThisMonth' => $salesThisMonth,
            'salesThisYear' => $salesThisYear,
            'purchasesThisMonth' => $purchasesThisMonth,
            'salesGrowth' => $salesGrowth,
            'transactionCount' => $transactionCount,
            'averageTransaction' => $averageTransaction,
            'purchaseTotal' => $purchaseTotal,
            'expenseTotal' => $expenseTotal,
            'otherIncomeTotal' => $otherIncomeTotal,
            'grossProfit' => $grossProfit,
            'netProfit' => $netProfit,
            'productCount' => $productCount,
            'stockQuantity' => $stockQuantity,
            'inventoryValue' => $inventoryValue,
            'lowStockTotal' => $lowStockTotal,
            'outOfStockTotal' => $outOfStockTotal,
            'customerCount' => $customerCount,
            'newCustomers' => $newCustomers,
            'returningCustomers' => $returningCustomers,
            'totalEmployees' => $totalEmployees,
            'supplierCount' => $supplierCount,
            'unpaidSales' => $unpaidSales,
            'unpaidExpenses' => $unpaidExpenses,
            'salesByPaymentMethod' => $salesByPaymentMethod,
            'salesByCashier' => $salesByCashier,
            'topProducts' => $topProducts,
            'slowMovingProducts' => $slowMovingProducts,
            'topCustomers' => $topCustomers,
            'shopPerformance' => $shopPerformance,
            'inventoryByShop' => $inventoryByShop,
            'lowStockProducts' => $lowStockProducts,
            'chartRows' => $chartRows,
            'expenseCategories' => $expenseCategories,
            'purchasesBySupplier' => $purchasesBySupplier,
            'activities' => $activities,
            'recentPurchases' => $recentPurchases,
        ]);
    }

    public function storeShop(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'string', 'max:255'],
            'registration_number' => ['required', 'string', 'max:255', 'unique:shops,registration_number'],
            'tin_number' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:shops,email'],
            'phone' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
        ]);

        $user = $request->user();
        $shop = DB::transaction(function () use ($validated, $user): Shop {
            $shop = Shop::create([
                ...$validated,
                'slug' => Str::slug($validated['business_name']) . '-' . Str::lower(Str::random(6)),
                'status' => 'pending',
                'created_by' => $user->id,
            ]);

            $user->shops()->syncWithoutDetaching([$shop->id]);

            if ($user->shop_id === null) {
                $user->update(['shop_id' => $shop->id]);
            }

            return $shop;
        });

        return redirect()
            ->route('business.dashboard')
            ->with('success', __(':shop has been added and is awaiting approval.', ['shop' => $shop->business_name]));
    }

    public function selectShop(Request $request, Shop $shop): RedirectResponse
    {
        abort_unless(
            $request->user()->shops()->whereKey($shop->getKey())->exists(),
            403
        );

        if (! $shop->isApproved()) {
            return redirect()
                ->route('business.dashboard')
                ->with('error', __('Only approved shops can be opened.'));
        }

        $request->session()->put('active_business_shop_id', $shop->id);

        return redirect()->route('shop.dashboard');
    }
}

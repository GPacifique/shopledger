<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
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

        // Get shop statistics
        $stats = [
            'total_products' => Product::where('shop_id', $shop->id)->count(),
            'total_suppliers' => Supplier::where('shop_id', $shop->id)->count(),
            'total_staff' => User::where('shop_id', $shop->id)->where('id', '!=', $user->id)->count(),
            'low_stock_products' => Product::where('shop_id', $shop->id)->where('stock', '<', 10)->count(),
        ];

        // Today's stats
        $today = Carbon::today();
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

        return view('dashboard.shop-admin', compact(
            'shop',
            'stats',
            'recentSales',
            'recentPurchases',
            'lowStockProducts',
            'staff',
            'chartData',
            'monthlyChartData'
        ));
    }

    private function getChartData($shopId, $days)
    {
        $labels = [];
        $salesData = [];
        $purchasesData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('M d');

            $salesData[] = Sale::where('shop_id', $shopId)
                ->whereDate('sale_date', $date)
                ->sum('total_amount');

            $purchasesData[] = Purchase::where('shop_id', $shopId)
                ->whereDate('purchase_date', $date)
                ->sum('total_amount');
        }

        return [
            'labels' => $labels,
            'sales' => $salesData,
            'purchases' => $purchasesData,
        ];
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
}

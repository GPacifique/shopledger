<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SellerController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $shop = $user->shop;

        if (!$shop || $shop->status !== 'approved') {
            return view('dashboard.user', compact('shop'));
        }

        $today = Carbon::today();
        $shopId = $shop->id;

        $todaySales = Sale::where('shop_id', $shopId)
            ->whereDate('sale_date', $today)
            ->sum('total_amount');

        $todaySalesCount = Sale::where('shop_id', $shopId)
            ->whereDate('sale_date', $today)
            ->count();

        $todayMySales = Sale::where('shop_id', $shopId)
            ->where('created_by', $user->id)
            ->whereDate('sale_date', $today)
            ->sum('total_amount');

        $todayMySalesCount = Sale::where('shop_id', $shopId)
            ->where('created_by', $user->id)
            ->whereDate('sale_date', $today)
            ->count();

        $averageSaleValue = $todaySalesCount > 0 ? $todaySales / $todaySalesCount : 0;

        $todayPurchases = Purchase::where('shop_id', $shopId)
            ->whereDate('purchase_date', $today)
            ->sum('total_amount');

        $totalProducts = Product::where('shop_id', $shopId)->count();
        $totalStockUnits = max(0, Product::where('shop_id', $shopId)->sum('stock'));
        $lowStockProducts = Product::where('shop_id', $shopId)
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->where('stock', '>', 0)
            ->count();
        $outOfStockProducts = Product::where('shop_id', $shopId)
            ->where('stock', '<=', 0)
            ->count();
        $inventoryValue = max(0, Product::where('shop_id', $shopId)
            ->sum(DB::raw('stock * buying_price')));

        $lowStockItems = Product::where('shop_id', $shopId)
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->where('stock', '>', 0)
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        $recentSales = Sale::where('shop_id', $shopId)
            ->where('created_by', $user->id)
            ->with('items.product')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $weeklySalesTrend = collect(range(6, 0, -1))->map(function ($daysAgo) use ($shopId, $user) {
            $date = Carbon::today()->subDays($daysAgo);
            $total = Sale::where('shop_id', $shopId)
                ->where('created_by', $user->id)
                ->whereDate('sale_date', $date)
                ->sum('total_amount');

            return [
                'day' => $date->format('D'),
                'date' => $date->format('M') . ' ' . $date->format('d'),
                'total' => (float) $total,
            ];
        });

        $weeklySalesMax = $weeklySalesTrend->max('total') ?: 1;
        $hasWeeklySales = $weeklySalesTrend->contains(fn ($trend) => (float) $trend['total'] > 0);

        $recentStockMovements = StockMovement::where('shop_id', $shopId)
            ->with('product')
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        $bestSellingProducts = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.shop_id', $shopId)
            ->where('sales.created_by', $user->id)
            ->selectRaw('sale_items.product_id, SUM(sale_items.quantity) as total_quantity, SUM(sale_items.line_total) as total_revenue')
            ->groupBy('sale_items.product_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $item->product = Product::find($item->product_id);
                return $item;
            });

        $products = Product::where('shop_id', $shopId)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        return view('dashboard.seller', compact(
            'shop',
            'todaySales',
            'todaySalesCount',
            'todayMySales',
            'todayMySalesCount',
            'averageSaleValue',
            'todayPurchases',
            'totalProducts',
            'totalStockUnits',
            'lowStockProducts',
            'outOfStockProducts',
            'inventoryValue',
            'lowStockItems',
            'recentSales',
            'weeklySalesTrend',
            'weeklySalesMax',
            'hasWeeklySales',
            'recentStockMovements',
            'bestSellingProducts',
            'products'
        ));
    }
}

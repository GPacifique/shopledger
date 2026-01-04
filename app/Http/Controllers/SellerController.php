<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

        // Today's sales by this seller
        $todaySales = Sale::where('shop_id', $shop->id)
            ->where('created_by', $user->id)
            ->whereDate('sale_date', $today)
            ->sum('total_amount');

        $todaySalesCount = Sale::where('shop_id', $shop->id)
            ->where('created_by', $user->id)
            ->whereDate('sale_date', $today)
            ->count();

        // Recent sales by this seller
        $recentSales = Sale::where('shop_id', $shop->id)
            ->where('created_by', $user->id)
            ->with('items.product')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        // Products for quick sale
        $products = Product::where('shop_id', $shop->id)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        return view('dashboard.seller', compact('shop', 'todaySales', 'todaySalesCount', 'recentSales', 'products'));
    }
}

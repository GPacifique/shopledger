<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AccountantController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $shop = $user->shop;

        if (!$shop || $shop->status !== 'approved') {
            return view('dashboard.user', compact('shop'));
        }

        $today = Carbon::today();

        // Today stats
        $todaySales = Sale::where('shop_id', $shop->id)->whereDate('sale_date', $today)->sum('total_amount');
        $todayPurchases = Purchase::where('shop_id', $shop->id)->whereDate('purchase_date', $today)->sum('total_amount');

        // Weekly stats
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $weeklySales = Sale::where('shop_id', $shop->id)->whereBetween('sale_date', [$weekStart, $weekEnd])->sum('total_amount');
        $weeklyPurchases = Purchase::where('shop_id', $shop->id)->whereBetween('purchase_date', [$weekStart, $weekEnd])->sum('total_amount');

        // Monthly stats
        $monthSales = Sale::where('shop_id', $shop->id)
            ->whereMonth('sale_date', $today->month)
            ->whereYear('sale_date', $today->year)
            ->sum('total_amount');
        $monthPurchases = Purchase::where('shop_id', $shop->id)
            ->whereMonth('purchase_date', $today->month)
            ->whereYear('purchase_date', $today->year)
            ->sum('total_amount');

        // Yearly stats
        $yearSales = Sale::where('shop_id', $shop->id)->whereYear('sale_date', $today->year)->sum('total_amount');
        $yearPurchases = Purchase::where('shop_id', $shop->id)->whereYear('purchase_date', $today->year)->sum('total_amount');

        // Profit calculation
        $monthProfit = SaleItem::whereHas('sale', function ($q) use ($shop, $today) {
            $q->where('shop_id', $shop->id)
              ->whereMonth('sale_date', $today->month)
              ->whereYear('sale_date', $today->year);
        })->selectRaw('SUM(quantity * (unit_price - cost_price_at_sale)) as profit')->value('profit') ?? 0;

        return view('dashboard.accountant', compact(
            'shop',
            'todaySales', 'todayPurchases',
            'weeklySales', 'weeklyPurchases',
            'monthSales', 'monthPurchases', 'monthProfit',
            'yearSales', 'yearPurchases'
        ));
    }
}

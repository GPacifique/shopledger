<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccountantController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $shop = $user->shop;

        if (!$shop || $shop->status !== 'approved') {
            return view('dashboard.user', compact('shop'));
        }

        $shopId = $shop->id;
        $today = Carbon::today();

        // TODAY
        $todaySales = Sale::where('shop_id', $shopId)
            ->whereDate('sale_date', $today)
            ->sum('total_amount');

        $todayPurchases = Purchase::where('shop_id', $shopId)
            ->whereDate('purchase_date', $today)
            ->sum('total_amount');

        // WEEK
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $weeklySales = Sale::where('shop_id', $shopId)
            ->whereBetween('sale_date', [$weekStart, $weekEnd])
            ->sum('total_amount');

        $weeklyPurchases = Purchase::where('shop_id', $shopId)
            ->whereBetween('purchase_date', [$weekStart, $weekEnd])
            ->sum('total_amount');

        // MONTH
        $monthSales = Sale::where('shop_id', $shopId)
            ->whereMonth('sale_date', $today->month)
            ->whereYear('sale_date', $today->year)
            ->sum('total_amount');

        $monthPurchases = Purchase::where('shop_id', $shopId)
            ->whereMonth('purchase_date', $today->month)
            ->whereYear('purchase_date', $today->year)
            ->sum('total_amount');

        // YEAR
        $yearSales = Sale::where('shop_id', $shopId)
            ->whereYear('sale_date', $today->year)
            ->sum('total_amount');

        $yearPurchases = Purchase::where('shop_id', $shopId)
            ->whereYear('purchase_date', $today->year)
            ->sum('total_amount');

        // PROFIT
        $monthProfit = SaleItem::whereHas('sale', function ($q) use ($shopId, $today) {
                $q->where('shop_id', $shopId)
                  ->whereMonth('sale_date', $today->month)
                  ->whereYear('sale_date', $today->year);
            })
            ->selectRaw('SUM(quantity * (unit_price - cost_price_at_sale)) as profit')
            ->value('profit') ?? 0;

        // MONTHLY CHART DATA
        $monthlySales = Sale::selectRaw('MONTH(sale_date) as month, SUM(total_amount) as total')
            ->where('shop_id', $shopId)
            ->whereYear('sale_date', $today->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyExpenses = Expense::selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
            ->where('shop_id', $shopId)
            ->whereYear('expense_date', $today->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        return view('dashboard.accountant', compact(
            'shop',
            'todaySales',
            'todayPurchases',
            'weeklySales',
            'weeklyPurchases',
            'monthSales',
            'monthPurchases',
            'monthProfit',
            'yearSales',
            'yearPurchases',
            'monthlySales',
            'monthlyExpenses'
        ));
    }
}
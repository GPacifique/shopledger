<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\SaleItem;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StatsController extends Controller
{
    public function summary(Request $request)
    {
        $request->validate([
            'period' => 'required|in:daily,weekly,monthly,yearly',
        ]);

        $shopId = $request->user()->shop_id;
        [$start, $end] = $this->periodBounds($request->period);

        $purchaseTotal = Purchase::where('shop_id', $shopId)
            ->whereBetween('purchase_date', [$start, $end])
            ->sum('total_amount');

        $saleTotal = Sale::where('shop_id', $shopId)
            ->whereBetween('sale_date', [$start, $end])
            ->sum('total_amount');

        $profit = SaleItem::whereHas('sale', function ($q) use ($shopId, $start, $end) {
                $q->where('shop_id', $shopId)
                  ->whereBetween('sale_date', [$start, $end]);
            })
            ->selectRaw('SUM(quantity * (unit_price - cost_price_at_sale)) as profit')
            ->value('profit') ?? 0;

        return response()->json([
            'period' => $request->period,
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'purchases' => (float) $purchaseTotal,
            'sales' => (float) $saleTotal,
            'profit' => (float) $profit,
        ]);
    }

    protected function periodBounds(string $period): array
    {
        $today = Carbon::today();
        return match ($period) {
            'daily' => [$today, $today],
            'weekly' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'monthly' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'yearly' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
        };
    }
}

<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\OtherIncome;
use App\Models\Purchase;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    public function generate(?int $shopId, Carbon $startDate, Carbon $endDate): array
    {
        return [
            'sales' => $this->sales($shopId, $startDate, $endDate),
            'purchases' => $this->purchases($shopId, $startDate, $endDate),
            'expenses' => $this->expenses($shopId, $startDate, $endDate),
            'other_income' => $this->otherIncome($shopId, $startDate, $endDate),
            'summary' => $this->summary($shopId, $startDate, $endDate),
            'daily_breakdown' => $this->dailyBreakdown($shopId, $startDate, $endDate),
        ];
    }

    public function sales(?int $shopId, Carbon $startDate, Carbon $endDate): array
    {
        $query = Sale::query()
            ->with(['items.product', 'customer', 'shop'])
            ->whereBetween('sale_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ]);

        if ($shopId !== null) {
            $query->where('shop_id', $shopId);
        }

        $sales = $query->latest('sale_date')->latest('id')->get();
        $items = collect();

        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $items->push([
                    'sale' => $sale,
                    'item' => $item,
                    'product' => $item->product,
                ]);
            }
        }

        $quantity = (float) $items->sum(fn ($row) => (float) ($row['item']->quantity ?? 0));
        $revenue = (float) $sales->sum(fn ($sale) => (float) ($sale->total_amount ?? 0));
        $cost = (float) $items->sum(function ($row) {
            return (float) ($row['item']->quantity ?? 0)
                * (float) ($row['item']->cost_price_at_sale ?? 0);
        });

        return [
            'sales' => $sales,
            'orders' => $sales, // Backward-compatible with existing views.
            'items' => $items,
            'count' => $sales->count(),
            'order_count' => $sales->count(),
            'quantity' => $quantity,
            'total' => $revenue,
            'revenue' => $revenue,
            'cost' => $cost,
            'cogs' => $cost,
            'gross_profit' => $revenue - $cost,
        ];
    }

    public function purchases(?int $shopId, Carbon $startDate, Carbon $endDate): array
    {
        $query = Purchase::query()
            ->with(['items.product', 'supplier', 'shop'])
            ->whereBetween('purchase_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ]);

        if ($shopId !== null) {
            $query->where('shop_id', $shopId);
        }

        $purchases = $query->latest('purchase_date')->latest('id')->get();
        $items = collect();

        foreach ($purchases as $purchase) {
            foreach ($purchase->items as $item) {
                $items->push([
                    'purchase' => $purchase,
                    'item' => $item,
                    'product' => $item->product,
                ]);
            }
        }

        return [
            'purchases' => $purchases,
            'items' => $items,
            'count' => $purchases->count(),
            'quantity' => (float) $items->sum(fn ($row) => (float) ($row['item']->quantity ?? 0)),
            'total' => (float) $purchases->sum(fn ($purchase) => (float) ($purchase->total_amount ?? 0)),
        ];
    }

    public function expenses(?int $shopId, Carbon $startDate, Carbon $endDate): array
    {
        $query = Expense::query()
            ->with(['category', 'shop', 'creator'])
            ->whereBetween('expense_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ]);

        if ($shopId !== null) {
            $query->where('shop_id', $shopId);
        }

        $expenses = $query->latest('expense_date')->latest('id')->get();

        return [
            'expenses' => $expenses,
            'count' => $expenses->count(),
            'total' => (float) $expenses->sum(fn ($expense) => (float) ($expense->amount ?? 0)),
        ];
    }

    public function otherIncome(?int $shopId, Carbon $startDate, Carbon $endDate): array
    {
        $query = OtherIncome::query()
            ->with(['category', 'shop', 'creator'])
            ->whereBetween('income_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ]);

        if ($shopId !== null) {
            $query->where('shop_id', $shopId);
        }

        $income = $query->latest('income_date')->latest('id')->get();

        return [
            'items' => $income,
            'count' => $income->count(),
            'total' => (float) $income->sum(fn ($item) => (float) ($item->amount ?? 0)),
        ];
    }

    public function summary(?int $shopId, Carbon $startDate, Carbon $endDate): array
    {
        $sales = $this->sales($shopId, $startDate, $endDate);
        $purchases = $this->purchases($shopId, $startDate, $endDate);
        $expenses = $this->expenses($shopId, $startDate, $endDate);
        $otherIncome = $this->otherIncome($shopId, $startDate, $endDate);

        $grossProfit = $sales['total'] - $sales['cost'];
        $netProfit = $grossProfit - $expenses['total'] + $otherIncome['total'];

        return [
            'sales' => $sales['total'],
            'sales_cost' => $sales['cost'],
            'cogs' => $sales['cost'],
            'gross_profit' => $grossProfit,
            'purchases' => $purchases['total'],
            'expenses' => $expenses['total'],
            'other_income' => $otherIncome['total'],
            'orders' => $sales['order_count'],
            'sales_count' => $sales['count'],
            'products_sold' => $sales['quantity'],
            'products_purchased' => $purchases['quantity'],
            'net_profit' => $netProfit,
        ];
    }

    public function dailyBreakdown(?int $shopId, Carbon $startDate, Carbon $endDate): Collection
    {
        $days = collect();
        $date = $startDate->copy()->startOfDay();
        $lastDate = $endDate->copy()->startOfDay();

        while ($date->lte($lastDate)) {
            $summary = $this->summary(
                $shopId,
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay()
            );

            $days->push([
                'date' => $date->copy(),
                'sales' => $summary['sales'],
                'purchases' => $summary['purchases'],
                'expenses' => $summary['expenses'],
                'other_income' => $summary['other_income'],
                'gross_profit' => $summary['gross_profit'],
                'net_profit' => $summary['net_profit'],
                'orders' => $summary['orders'],
                'products_sold' => $summary['products_sold'],
            ]);

            $date->addDay();
        }

        return $days;
    }
}

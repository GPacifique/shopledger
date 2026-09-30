<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\OtherIncome;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Generate the complete report.
     */
    public function generate(
        ?int $shopId,
        Carbon $startDate,
        Carbon $endDate
    ): array {

        return [
            'sales' => $this->sales(
                $shopId,
                $startDate,
                $endDate
            ),

            'purchases' => $this->purchases(
                $shopId,
                $startDate,
                $endDate
            ),

            'expenses' => $this->expenses(
                $shopId,
                $startDate,
                $endDate
            ),

            'other_income' => $this->otherIncome(
                $shopId,
                $startDate,
                $endDate
            ),

            'summary' => $this->summary(
                $shopId,
                $startDate,
                $endDate
            ),

            'daily_breakdown' => $this->dailyBreakdown(
                $shopId,
                $startDate,
                $endDate
            ),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SALES
    |--------------------------------------------------------------------------
    */

    public function sales(
        ?int $shopId,
        Carbon $startDate,
        Carbon $endDate
    ): array {

        $query = Order::query()
            ->with([
                'items.product',
                'user',
            ])
            ->whereBetween(
                'created_at',
                [$startDate, $endDate]
            );

        if ($shopId) {
            $query->where('shop_id', $shopId);
        }

        $orders = $query
            ->latest()
            ->get();

        $items = collect();

        foreach ($orders as $order) {

            foreach ($order->items ?? [] as $item) {

                $items->push([
                    'order' => $order,
                    'item' => $item,
                    'product' => $item->product ?? null,
                ]);
            }
        }

        return [
            'orders' => $orders,
            'items' => $items,

            'order_count' => $orders->count(),

            'quantity' => $items->sum(function ($row) {
                return (float) ($row['item']->quantity ?? 0);
            }),

            'total' => $orders->sum(function ($order) {
                return (float) (
                    $order->total
                    ?? $order->total_amount
                    ?? $order->grand_total
                    ?? 0
                );
            }),

            'cost' => $items->sum(function ($row) {

                $item = $row['item'];
                $product = $row['product'];

                $quantity = (float) (
                    $item->quantity ?? 0
                );

                $buyingPrice = (float) (
                    $item->buying_price
                    ?? $item->cost_price
                    ?? $product?->buying_price
                    ?? $product?->purchase_price
                    ?? 0
                );

                return $quantity * $buyingPrice;
            }),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | PURCHASES
    |--------------------------------------------------------------------------
    */

    public function purchases(
        ?int $shopId,
        Carbon $startDate,
        Carbon $endDate
    ): array {

        $query = Purchase::query()
            ->with([
                'items.product',
                'supplier',
            ])
            ->whereBetween(
                'created_at',
                [$startDate, $endDate]
            );

        if ($shopId) {
            $query->where('shop_id', $shopId);
        }

        $purchases = $query
            ->latest()
            ->get();

        $items = collect();

        foreach ($purchases as $purchase) {

            foreach ($purchase->items ?? [] as $item) {

                $items->push([
                    'purchase' => $purchase,
                    'item' => $item,
                    'product' => $item->product ?? null,
                ]);
            }
        }

        return [
            'purchases' => $purchases,

            'items' => $items,

            'count' => $purchases->count(),

            'quantity' => $items->sum(function ($row) {
                return (float) ($row['item']->quantity ?? 0);
            }),

            'total' => $purchases->sum(function ($purchase) {
                return (float) (
                    $purchase->total
                    ?? $purchase->total_amount
                    ?? $purchase->grand_total
                    ?? 0
                );
            }),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | EXPENSES
    |--------------------------------------------------------------------------
    */

    public function expenses(
        ?int $shopId,
        Carbon $startDate,
        Carbon $endDate
    ): array {

        $query = Expense::query()
            ->whereBetween(
                'created_at',
                [$startDate, $endDate]
            );

        if ($shopId) {
            $query->where('shop_id', $shopId);
        }

        $expenses = $query
            ->latest()
            ->get();

        return [
            'expenses' => $expenses,

            'count' => $expenses->count(),

            'total' => $expenses->sum(function ($expense) {
                return (float) (
                    $expense->amount
                    ?? $expense->total
                    ?? 0
                );
            }),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | OTHER INCOME
    |--------------------------------------------------------------------------
    */

    public function otherIncome(
        ?int $shopId,
        Carbon $startDate,
        Carbon $endDate
    ): array {

        $query = OtherIncome::query()
            ->whereBetween(
                'created_at',
                [$startDate, $endDate]
            );

        if ($shopId) {
            $query->where('shop_id', $shopId);
        }

        $income = $query
            ->latest()
            ->get();

        return [
            'items' => $income,

            'count' => $income->count(),

            'total' => $income->sum(function ($item) {
                return (float) (
                    $item->amount
                    ?? $item->total
                    ?? 0
                );
            }),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | SUMMARY
    |--------------------------------------------------------------------------
    */

    public function summary(
        ?int $shopId,
        Carbon $startDate,
        Carbon $endDate
    ): array {

        $sales = $this->sales(
            $shopId,
            $startDate,
            $endDate
        );

        $purchases = $this->purchases(
            $shopId,
            $startDate,
            $endDate
        );

        $expenses = $this->expenses(
            $shopId,
            $startDate,
            $endDate
        );

        $otherIncome = $this->otherIncome(
            $shopId,
            $startDate,
            $endDate
        );

        $grossProfit =
            $sales['total']
            - $sales['cost'];

        $netProfit =
            $grossProfit
            - $expenses['total']
            + $otherIncome['total'];

        return [

            'sales' => $sales['total'],

            'sales_cost' => $sales['cost'],

            'gross_profit' => $grossProfit,

            'purchases' => $purchases['total'],

            'expenses' => $expenses['total'],

            'other_income' => $otherIncome['total'],

            'orders' => $sales['order_count'],

            'products_sold' => $sales['quantity'],

            'net_profit' => $netProfit,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | DAILY BREAKDOWN
    |--------------------------------------------------------------------------
    */

    public function dailyBreakdown(
        ?int $shopId,
        Carbon $startDate,
        Carbon $endDate
    ): Collection {

        $days = collect();

        $date = $startDate->copy()->startOfDay();

        while ($date <= $endDate) {

            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $summary = $this->summary(
                $shopId,
                $dayStart,
                $dayEnd
            );

            $days->push([
                'date' => $date->copy(),

                'sales' => $summary['sales'],

                'purchases' => $summary['purchases'],

                'expenses' => $summary['expenses'],

                'other_income' => $summary['other_income'],

                'gross_profit' => $summary['gross_profit'],

                'net_profit' => $summary['net_profit'],
            ]);

            $date->addDay();
        }

        return $days;
    }
}
<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    protected Collection $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Order',
            'Product',
            'SKU',
            'Quantity',
            'Buying Price',
            'Selling Price',
            'Revenue',
            'Profit',
        ];
    }

    public function map($row): array
    {
        $item = $row['item'] ?? null;

        $product = $row['product'] ?? null;

        $sale = $row['sale'] ?? $row['order'] ?? null;

        $quantity = (float) (
            $item->quantity ?? 0
        );

        $buyingPrice = (float) (
            $item?->cost_price_at_sale
            ?? $item?->buying_price
            ?? $item?->cost_price
            ?? $product?->buying_price
            ?? 0
        );

        $sellingPrice = (float) (
            $item?->unit_price
            ?? $item?->selling_price
            ?? $item?->price
            ?? 0
        );

        $revenue = $quantity * $sellingPrice;

        $cost = $quantity * $buyingPrice;

        return [
            $sale?->sale_date
                ? \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d H:i')
                : ($sale?->created_at?->format('Y-m-d H:i') ?? ''),

            $sale?->id ?? '',

            $product?->name ?? 'Unknown Product',

            $product?->sku ?? $item?->sku ?? '',

            $quantity,

            $buyingPrice,

            $sellingPrice,

            $revenue,

            $revenue - $cost,
        ];
    }
}
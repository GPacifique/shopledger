<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        private readonly ?int $shopId = null
    ) {
    }

    public function collection()
    {
        $query = Product::with([
            'category',
            'supplier',
        ])->orderBy('name');

        if ($this->shopId) {
            $query->where('shop_id', $this->shopId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Shop ID',
            'SKU',
            'Product Name',
            'Category',
            'Supplier',
            'Barcode',
            'QR Code',
            'Description',
            'Buying Price',
            'Selling Price',
            'Quantity',
            'Stock',
            'Minimum Stock',
            'Expiry Date',
            'Product Image',
            'Status',
            'Created At',
            'Updated At',
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->shop_id,
            $product->sku,
            $product->name,
            $product->category?->name ?? '',
            $product->supplier?->name ?? '',
            $product->barcode ?? '',
            $product->qr_code ?? '',
            $product->description ?? '',
            $product->buying_price,
            $product->selling_price,
            $product->quantity,
            $product->stock,
            $product->minimum_stock,
            $product->expiry_date?->format('Y-m-d'),
            $product->product_image ?? '',
            $product->status,
            $product->created_at?->format('Y-m-d H:i:s'),
            $product->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
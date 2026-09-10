<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ImportTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function __construct(
        private readonly string $type
    ) {
    }

    public function headings(): array
    {
        return match ($this->type) {
            'products' => [
                'SKU',
                'Product Name',
                'Category',
                'Supplier',
                'Barcode',
                'QR Code',
                'Description',
                'Buying Price',
                'Selling Price',
                'Opening Quantity',
                'Opening Unit Cost',
                'Minimum Stock',
                'Expiry Date',
                'Product Image',
                'Status',
            ],

            'suppliers' => [
                'Supplier Name',
                'Contact Name',
                'Email',
                'Phone',
                'Address',
            ],

            'customers' => [
                'Customer Name',
                'Phone',
                'Email',
                'Address',
            ],

            'purchases' => [
                'Purchase Reference',
                'Purchase Date',
                'Supplier',
                'Product SKU',
                'Quantity',
                'Unit Cost',
            ],

            'sales' => [
                'Sale Reference',
                'Sale Date',
                'Customer',
                'Product SKU',
                'Quantity',
                'Unit Price',
                'Payment Method',
                'Payment Status',
            ],

            default => [],
        };
    }

    public function array(): array
    {
        return [
            match ($this->type) {
                'products' => [
                    'PROD-001',
                    'Example Product',
                    'General',
                    '',
                    '',
                    '',
                    'Replace this example row',
                    1000,
                    1500,
                    10,
                    1000,
                    5,
                    '',
                    '',
                    'active',
                ],

                'suppliers' => [
                    'Example Supplier',
                    'John Doe',
                    'supplier@example.com',
                    '0780000000',
                    'Kigali',
                ],

                'customers' => [
                    'Example Customer',
                    '0780000001',
                    'customer@example.com',
                    'Kigali',
                ],

                'purchases' => [
                    'PUR-0001',
                    date('Y-m-d'),
                    'Example Supplier',
                    'PROD-001',
                    10,
                    1000,
                ],

                'sales' => [
                    'SALE-0001',
                    date('Y-m-d'),
                    'Example Customer',
                    'PROD-001',
                    2,
                    1500,
                    'cash',
                    'paid',
                ],

                default => [],
            },
        ];
    }
}
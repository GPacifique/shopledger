<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Sale;
use Illuminate\Contracts\View\View;

class DocumentVerificationController extends Controller
{
    public function show(string $uuid): View
    {
        $sale = Sale::query()->where('uuid', $uuid)->first();

        if ($sale) {
            return view('documents.verify', [
                'documentType' => 'Sale',
                'documentNumber' => 'SALE-' . str_pad((string) $sale->id, 8, '0', STR_PAD_LEFT),
                'documentDate' => $sale->sale_date->format('Y-m-d'),
                'documentStatus' => $sale->payment_status ?? 'Unknown',
            ]);
        }

        $order = Order::query()->where('uuid', $uuid)->firstOrFail();

        return view('documents.verify', [
            'documentType' => 'Order',
            'documentNumber' => (string) $order->order_number,
            'documentDate' => $order->created_at->format('Y-m-d'),
            'documentStatus' => ucfirst((string) $order->status),
        ]);
    }
}

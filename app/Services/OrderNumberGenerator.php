<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;

class OrderNumberGenerator
{
    /**
     * Generate the next sequential order number for a shop, e.g. "SHP1-0001".
     * Uses a row lock on the last order for this shop to avoid race conditions
     * when two orders are created concurrently.
     */
    public function generate(Shop $shop): string
    {
        return DB::transaction(function () use ($shop) {
            $lastNumber = Order::where('shop_id', $shop->id)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('order_number');

            $nextSequence = 1;

            if ($lastNumber && preg_match('/(\d+)$/', $lastNumber, $matches)) {
                $nextSequence = (int) $matches[1] + 1;
            }

            $prefix = $shop->code ?? ('SHP' . $shop->id);

            return sprintf('%s-%04d', $prefix, $nextSequence);
        });
    }
}

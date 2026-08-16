<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderApprovalService
{
    /**
     * Seller approves a waiter's order:
     *  - locks the order + each product row
     *  - checks stock, deducts it, logs a StockMovement per item
     *  - creates a Sale + SaleItem records mirroring the order
     *  - marks the order 'approved' and links it to the new sale
     *
     * All inside one DB transaction — if anything fails (e.g. insufficient
     * stock on item 3 of 5), nothing is deducted and no Sale is created.
     */
    public function approve(Order $order, User $seller, ?string $paymentMethod = null): Order
    {
        if (!$order->canBeReviewed()) {
            throw new \InvalidArgumentException(
                "Order {$order->order_number} is '{$order->status}' and cannot be approved."
            );
        }

        return DB::transaction(function () use ($order, $seller, $paymentMethod) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $items = $order->items()->with('product')->get();

            // 1. Validate + deduct stock for every item first (fail before creating anything)
            foreach ($items as $item) {
                if (!$item->product_id) {
                    continue; // custom line item, nothing to deduct
                }

                $product = $item->product->newQuery()
                    ->whereKey($item->product_id)
                    ->lockForUpdate()
                    ->first();

                if ($product->stock < $item->quantity) {
                    throw new InsufficientStockException($product, (int) $item->quantity);
                }
            }

            // 2. Create the Sale record
            $sale = Sale::create([
                'shop_id' => $order->shop_id,
                'customer_id' => $order->customer_id,
                'sale_date' => now()->toDateString(),
                'total_amount' => $order->total_amount,
                'payment_method' => $paymentMethod ?? $order->payment_method,
                'payment_status' => $order->payment_status,
                'created_by' => $seller->id,
            ]);

            // link the sale back to the order so the migration's order_id is populated
            $sale->order_id = $order->id;
            $sale->save();

            // 3. Create SaleItems + deduct stock + log movements
            foreach ($items as $item) {
                $product = $item->product_id
                    ? $item->product->newQuery()->whereKey($item->product_id)->lockForUpdate()->first()
                    : null;

                $sale->items()->create([
                    'shop_id' => $order->shop_id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'cost_price_at_sale' => $product?->buying_price ?? 0,
                    'line_total' => $item->line_total,
                ]);

                if ($product) {
                    $product->decrement('stock', $item->quantity);

                    StockMovement::create([
                        'shop_id' => $order->shop_id,
                        'product_id' => $product->id,
                        'reference_type' => 'order',
                        'reference_id' => $order->id,
                        'quantity_change' => -$item->quantity,
                        'quantity_after' => $product->fresh()->stock,
                        'created_by' => $seller->id,
                        'note' => "Order {$order->order_number} approved by {$seller->name}",
                    ]);
                }
            }

            // 4. Mark the order approved and link it to the sale
            $order->update([
                'status' => 'approved',
                'reviewed_by' => $seller->id,
                'reviewed_at' => now(),
                'sale_id' => $sale->id,
                'payment_method' => $sale->payment_method,
            ]);

            return $order->fresh(['items', 'sale.items']);
        });
    }

    /**
     * Seller declines a waiter's order. No stock or sales effect.
     */
    public function reject(Order $order, User $seller, string $reason): Order
    {
        if (!$order->canBeReviewed()) {
            throw new \InvalidArgumentException(
                "Order {$order->order_number} is '{$order->status}' and cannot be rejected."
            );
        }

        $order->update([
            'status' => 'rejected',
            'reviewed_by' => $seller->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return $order;
    }

    /**
     * Waiter (or admin) withdraws an order before the seller has acted on it.
     */
    public function cancel(Order $order): Order
    {
        if (!$order->canBeCancelled()) {
            throw new \InvalidArgumentException(
                "Order {$order->order_number} is '{$order->status}' and cannot be cancelled."
            );
        }

        $order->update(['status' => 'cancelled']);

        return $order;
    }
}

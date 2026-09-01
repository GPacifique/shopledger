<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class OrderStatusService
{
    /**
     * Transition an order to a new status, applying stock effects where needed.
     *
     * - pending/processing -> completed  : deduct stock
     * - completed -> cancelled/refunded  : restock
     *
     * Wrapped in a DB transaction with row locks on the products being touched,
     * so two orders completing at the same time can't oversell the same item.
     */
    public function transitionTo(Order $order, string $newStatus, ?string $note = null): Order
    {
        if (!$order->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException(
                "Cannot transition order from '{$order->status}' to '{$newStatus}'."
            );
        }

        return DB::transaction(function () use ($order, $newStatus, $note) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($newStatus === 'completed') {
                $this->deductStock($order, $note);
                $order->completed_at = now();
            }

            if ($order->status === 'completed' && in_array($newStatus, ['cancelled', 'refunded'], true)) {
                $this->restock($order, $note);
            }

            $order->status = $newStatus;
            $order->save();

            return $order->fresh(['items', 'payments']);
        });
    }

    private function deductStock(Order $order, ?string $note): void
    {
        foreach ($order->items()->with('product')->get() as $item) {
            $product = $item->product;

            if (!$product) {
                continue; // custom line item with no catalog product, nothing to deduct
            }

            // lock the product row to prevent concurrent oversell
            $product = $product->newQuery()->whereKey($product->id)->lockForUpdate()->first();

            if ($product->stock_quantity < $item->quantity) {
                throw new InsufficientStockException($product, $item->quantity);
            }

            $product->decrement('stock_quantity', $item->quantity);

            StockMovement::create([
                'shop_id' => $order->shop_id,
                'product_id' => $product->id,
                'reference_type' => 'order',
                'reference_id' => $order->id,
                'quantity_change' => -$item->quantity,
                'quantity_after' => $product->fresh()->stock_quantity,
                'created_by' => auth()->id(),
                'note' => $note ?? "Order {$order->order_number} completed",
            ]);
        }
    }

    private function restock(Order $order, ?string $note): void
    {
        foreach ($order->items()->with('product')->get() as $item) {
            $product = $item->product;

            if (!$product) {
                continue;
            }

            $product = $product->newQuery()->whereKey($product->id)->lockForUpdate()->first();

            $product->increment('stock_quantity', $item->quantity);

            StockMovement::create([
                'shop_id' => $order->shop_id,
                'product_id' => $product->id,
                'reference_type' => 'order',
                'reference_id' => $order->id,
                'quantity_change' => $item->quantity,
                'quantity_after' => $product->fresh()->stock_quantity,
                'created_by' => auth()->id(),
                'note' => $note ?? "Order {$order->order_number} {$order->status}",
            ]);
        }
    }
}

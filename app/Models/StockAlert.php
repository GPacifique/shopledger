<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAlert extends Model
{
    protected $fillable = [
        'shop_id',
        'product_id',
        'alert_type',
        'current_stock',
        'threshold',
        'is_resolved',
        'resolved_at',
    ];

    protected $casts = [
        'current_stock' => 'decimal:2',
        'threshold' => 'decimal:2',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    /**
     * Alert types used by the stock system.
     */
    public const TYPE_LOW_STOCK = 'low_stock';
    public const TYPE_OUT_OF_STOCK = 'out_of_stock';

    /**
     * Shop relationship.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Product relationship.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check the product stock and create/update the appropriate alert.
     */
    public static function checkAndCreateAlert(Product $product): ?self
    {
        $currentStock = (float) $product->stock;
        $threshold = (float) ($product->low_stock_threshold ?? 0);

        /*
         * Determine alert type.
         */
        if ($currentStock <= 0) {
            $alertType = self::TYPE_OUT_OF_STOCK;
        } elseif ($threshold > 0 && $currentStock <= $threshold) {
            $alertType = self::TYPE_LOW_STOCK;
        } else {
            $alertType = null;
        }

        /*
         * Find any existing unresolved alert for this product.
         */
        $existingAlert = self::where('shop_id', $product->shop_id)
            ->where('product_id', $product->id)
            ->where('is_resolved', false)
            ->latest('id')
            ->first();

        /*
         * Stock is healthy.
         * Resolve any existing alert.
         */
        if ($alertType === null) {
            if ($existingAlert) {
                $existingAlert->update([
                    'current_stock' => $currentStock,
                    'is_resolved' => true,
                    'resolved_at' => now(),
                ]);
            }

            return null;
        }

        /*
         * If an alert already exists, update it.
         */
        if ($existingAlert) {

            /*
             * If the alert type changed, update it.
             */
            if ($existingAlert->alert_type !== $alertType) {
                $existingAlert->update([
                    'alert_type' => $alertType,
                    'current_stock' => $currentStock,
                    'threshold' => $threshold,
                    'is_resolved' => false,
                    'resolved_at' => null,
                ]);
            } else {
                $existingAlert->update([
                    'current_stock' => $currentStock,
                    'threshold' => $threshold,
                ]);
            }

            return $existingAlert->fresh();
        }

        /*
         * Create a new unresolved alert.
         */
        return self::create([
            'shop_id' => $product->shop_id,
            'product_id' => $product->id,
            'alert_type' => $alertType,
            'current_stock' => $currentStock,
            'threshold' => $threshold,
            'is_resolved' => false,
            'resolved_at' => null,
        ]);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'product_id',
        'alert_type',
        'current_stock',
        'threshold',
        'is_resolved',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
        'current_stock' => 'integer',
        'threshold' => 'integer',
    ];

    const TYPE_LOW_STOCK = 'low_stock';
    const TYPE_OUT_OF_STOCK = 'out_of_stock';
    const TYPE_OVERSTOCK = 'overstock';

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function resolve(int $userId): void
    {
        $this->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => $userId,
        ]);
    }

    public function getAlertColorAttribute(): string
    {
        return match ($this->alert_type) {
            self::TYPE_OUT_OF_STOCK => 'red',
            self::TYPE_LOW_STOCK => 'yellow',
            self::TYPE_OVERSTOCK => 'blue',
            default => 'gray',
        };
    }

    public function getAlertIconAttribute(): string
    {
        return match ($this->alert_type) {
            self::TYPE_OUT_OF_STOCK => 'x-circle',
            self::TYPE_LOW_STOCK => 'exclamation-triangle',
            self::TYPE_OVERSTOCK => 'arrow-trending-up',
            default => 'bell',
        };
    }

    public static function checkAndCreateAlert(Product $product): ?self
    {
        // Don't create alerts for products that don't track stock
        if (!$product->track_stock) {
            return null;
        }

        $existingAlert = self::where('product_id', $product->id)
            ->where('is_resolved', false)
            ->first();

        // Determine alert type
        $alertType = null;
        if ($product->stock <= 0) {
            $alertType = self::TYPE_OUT_OF_STOCK;
        } elseif ($product->stock <= $product->low_stock_threshold) {
            $alertType = self::TYPE_LOW_STOCK;
        }

        // If stock is now healthy, resolve existing alert
        if (!$alertType && $existingAlert) {
            $existingAlert->resolve(auth()->id() ?? 1);
            return null;
        }

        // If alert type changed, resolve old and create new
        if ($existingAlert && $existingAlert->alert_type !== $alertType) {
            $existingAlert->resolve(auth()->id() ?? 1);
            $existingAlert = null;
        }

        // Create new alert if needed
        if ($alertType && !$existingAlert) {
            return self::create([
                'shop_id' => $product->shop_id,
                'product_id' => $product->id,
                'alert_type' => $alertType,
                'current_stock' => $product->stock,
                'threshold' => $product->low_stock_threshold,
            ]);
        }

        // Update current stock on existing alert
        if ($existingAlert) {
            $existingAlert->update(['current_stock' => $product->stock]);
        }

        return $existingAlert;
    }
}

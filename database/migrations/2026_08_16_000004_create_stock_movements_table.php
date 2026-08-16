<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $table = 'stock_movements';

    protected $fillable = [
        'shop_id',
        'product_id',
        'reference_type',
        'reference_id',
        'quantity_change',
        'quantity_after',
        'created_by',
        'note',
    ];

    protected $casts = [
        'quantity_change' => 'decimal:2',
        'quantity_after' => 'decimal:2',
        'reference_id' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether stock was added.
     */
    public function isStockIn(): bool
    {
        return (float) $this->quantity_change > 0;
    }

    /**
     * Determine whether stock was removed.
     */
    public function isStockOut(): bool
    {
        return (float) $this->quantity_change < 0;
    }

    /**
     * Absolute quantity moved.
     */
    public function getQuantityAttribute(): float
    {
        return abs((float) $this->quantity_change);
    }

    /**
     * Human-readable movement direction.
     */
    public function getDirectionAttribute(): string
    {
        if ($this->isStockIn()) {
            return 'in';
        }

        if ($this->isStockOut()) {
            return 'out';
        }

        return 'none';
    }

    /**
     * Human-readable reference.
     */
    public function getReferenceLabelAttribute(): string
    {
        return match ($this->reference_type) {
            'order' => 'Sale / Order',
            'purchase' => 'Purchase',
            'restock' => 'Restock',
            'return' => 'Return',
            'transfer' => 'Transfer',
            'adjustment' => 'Manual Adjustment',
            'damage' => 'Damage',
            'sale' => 'Sale',
            default => ucfirst(str_replace('_', ' ', $this->reference_type)),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeStockIn($query)
    {
        return $query->where('quantity_change', '>', 0);
    }

    public function scopeStockOut($query)
    {
        return $query->where('quantity_change', '<', 0);
    }

    public function scopeForShop($query, int $shopId)
    {
        return $query->where('shop_id', $shopId);
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeReference($query, string $type)
    {
        return $query->where('reference_type', $type);
    }
}
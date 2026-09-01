<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'shop_id',
        'product_id',
        'reference_type',
        'reference_id',
        'quantity_change',
        'quantity_after',
        'created_by',
        'note',
        'type',
    ];

    protected $casts = [
        'quantity_change' => 'decimal:2',
        'quantity_after' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Movement Types
    |--------------------------------------------------------------------------
    */

    public const TYPE_PURCHASE = 'purchase';

    public const TYPE_SALE = 'sale';

    public const TYPE_ORDER = 'order';

    public const TYPE_ADJUSTMENT = 'adjustment';

    public const TYPE_RETURN = 'return';

    public const TYPE_TRANSFER = 'transfer';

    public const TYPE_OPENING = 'opening';

    public const TYPE_DAMAGE = 'damage';

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
    | Movement Types
    |--------------------------------------------------------------------------
    */

    public static function getTypes(): array
    {
        return [
            self::TYPE_PURCHASE => 'Purchase',
            self::TYPE_SALE => 'Sale',
            self::TYPE_ORDER => 'Order',
            self::TYPE_ADJUSTMENT => 'Stock Adjustment',
            self::TYPE_RETURN => 'Return',
            self::TYPE_TRANSFER => 'Transfer',
            self::TYPE_OPENING => 'Opening Stock',
            self::TYPE_DAMAGE => 'Damaged Stock',
        ];
    }

    /**
     * Get the human-readable movement type.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::getTypes()[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Determine whether this movement increases stock.
     */
    public function isIncoming(): bool
    {
        return in_array($this->type, [
            self::TYPE_PURCHASE,
            self::TYPE_RETURN,
            self::TYPE_OPENING,
        ], true);
    }

    /**
     * Determine whether this movement decreases stock.
     */
    public function isOutgoing(): bool
    {
        return in_array($this->type, [
            self::TYPE_SALE,
            self::TYPE_ORDER,
            self::TYPE_DAMAGE,
        ], true);
    }
}
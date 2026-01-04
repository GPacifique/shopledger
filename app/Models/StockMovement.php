<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'product_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reference_type',
        'reference_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
    ];

    const TYPE_PURCHASE = 'purchase';
    const TYPE_SALE = 'sale';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_RETURN = 'return';
    const TYPE_DAMAGE = 'damage';
    const TYPE_TRANSFER_IN = 'transfer_in';
    const TYPE_TRANSFER_OUT = 'transfer_out';

    public static function getTypes(): array
    {
        return [
            self::TYPE_PURCHASE => 'Purchase',
            self::TYPE_SALE => 'Sale',
            self::TYPE_ADJUSTMENT => 'Adjustment',
            self::TYPE_RETURN => 'Return',
            self::TYPE_DAMAGE => 'Damage',
            self::TYPE_TRANSFER_IN => 'Transfer In',
            self::TYPE_TRANSFER_OUT => 'Transfer Out',
        ];
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference()
    {
        if ($this->reference_type && $this->reference_id) {
            return $this->reference_type::find($this->reference_id);
        }
        return null;
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_PURCHASE, self::TYPE_TRANSFER_IN, self::TYPE_RETURN => 'green',
            self::TYPE_SALE, self::TYPE_TRANSFER_OUT => 'blue',
            self::TYPE_DAMAGE => 'red',
            self::TYPE_ADJUSTMENT => 'yellow',
            default => 'gray',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_PURCHASE => 'arrow-down-circle',
            self::TYPE_SALE => 'arrow-up-circle',
            self::TYPE_ADJUSTMENT => 'adjustments-horizontal',
            self::TYPE_RETURN => 'arrow-uturn-left',
            self::TYPE_DAMAGE => 'exclamation-triangle',
            self::TYPE_TRANSFER_IN => 'arrow-right-circle',
            self::TYPE_TRANSFER_OUT => 'arrow-left-circle',
            default => 'cube',
        };
    }

    public function isIncoming(): bool
    {
        return in_array($this->type, [self::TYPE_PURCHASE, self::TYPE_TRANSFER_IN, self::TYPE_RETURN]);
    }
}

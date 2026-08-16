<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'customer_id',
        'created_by',
        'reviewed_by',
        'sale_id',
        'order_number',
        'status',
        'payment_method',
        'payment_status',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'notes',
        'rejection_reason',
        'reviewed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public const STATUSES = ['pending', 'approved', 'rejected', 'cancelled'];

    // reuse Sale's exact keys/values so an approved order copies over cleanly
    public const PAYMENT_METHODS = Sale::PAYMENT_METHODS;
    public const PAYMENT_STATUSES = Sale::PAYMENT_STATUSES;

    // ── Relationships ──────────────────────────────────────

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function waiter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeForShop(Builder $query, int $shopId): Builder
    {
        return $query->where('shop_id', $shopId);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeByWaiter(Builder $query, int $userId): Builder
    {
        return $query->where('created_by', $userId);
    }

    // ── Helpers ────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeReviewed(): bool
    {
        return $this->isPending();
    }

    public function canBeCancelled(): bool
    {
        // only the waiter (or an admin) cancelling before a seller has acted on it
        return $this->isPending();
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->items->sum('line_total');
        $this->subtotal = $subtotal;
        $this->total_amount = $subtotal - $this->discount_amount + $this->tax_amount;
        $this->save();
    }
}

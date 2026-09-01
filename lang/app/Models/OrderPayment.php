<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'recorded_by',
        'amount',
        'payment_method',
        'reference',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public const METHODS = ['cash', 'mobile_money', 'card', 'bank_transfer', 'credit'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    protected static function booted(): void
    {
        // keep the parent order's amount_paid / payment_status in sync
        static::created(function (OrderPayment $payment) {
            $payment->order->increment('amount_paid', $payment->amount);
            $payment->order->refresh();
            $payment->order->update([
                'payment_status' => $payment->order->isFullyPaid() ? 'paid' : 'partial',
            ]);
        });

        static::deleted(function (OrderPayment $payment) {
            $payment->order->decrement('amount_paid', $payment->amount);
            $payment->order->refresh();
            $payment->order->update([
                'payment_status' => $payment->order->amount_paid <= 0
                    ? 'unpaid'
                    : ($payment->order->isFullyPaid() ? 'paid' : 'partial'),
            ]);
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'shop_id',
        'subscription_plan_id',
        'status',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'grace_ends_at',
        'cancelled_at',
        'auto_renew',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'grace_ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'auto_renew' => 'boolean',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['trial', 'active'], true)
            && $this->starts_at?->isPast()
            && $this->ends_at?->isFuture();
    }

    public function isExpired(): bool
    {
        if (in_array($this->status, ['expired', 'cancelled', 'suspended'], true)) {
            return true;
        }

        return $this->ends_at?->isPast()
            && ! $this->onGracePeriod();
    }

    public function daysRemaining(): int
    {
        if (! $this->ends_at) {
            return 0;
        }

        return (int) now()->startOfDay()->diffInDays(
            $this->ends_at->copy()->startOfDay(),
            false
        );
    }

    public function onGracePeriod(): bool
    {
        return $this->status === 'past_due'
            && $this->grace_ends_at?->isFuture();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $table = 'subscription_plans';

    protected $fillable = [
        'name',
        'slug',
        'price',
        'billing_period',
        'billing_cycle',
        'max_users',
        'max_products',
        'max_branches',
        'features',
        'is_active',
        'status',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function getBillingCycleAttribute(): string
    {
        return $this->billing_period ?? 'monthly';
    }

    public function getStatusAttribute(): string
    {
        return ($this->is_active ?? true) ? 'active' : 'inactive';
    }

    public function setBillingCycleAttribute(string $value): void
    {
        $this->attributes['billing_period'] = $value;
    }

    public function setStatusAttribute(string $value): void
    {
        $this->attributes['is_active'] = $value === 'active';
    }
}

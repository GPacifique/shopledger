<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSubscription extends Model
{
    protected $fillable = [
        'id',
        'shop_id',
        'subscriptionplan_id',
        'start_date',
        'end_date',
        'status',
        'payment_status',
        'amount_paid',
        'renewal_date',
        'created_at',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscriptionplan_id');
    }
}

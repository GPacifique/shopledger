<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use App\Models\ShopSubscription;
Use App\Models\Shop;
class SubscriptionPayment extends Model
{
    protected $fillable = [
        'shopsubscription_id',
        'shop_id',
        'amount',
        'payment_method',
        'transaction_reference',
        'status',
        'paid_at',
        'payment_date',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function subscription()
    {
        return $this->belongsTo(ShopSubscription::class, 'shopsubscription_id');
    }
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}

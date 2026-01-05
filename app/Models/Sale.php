<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'sale_date', 'total_amount', 'payment_method', 'created_by',
    ];

    public const PAYMENT_METHODS = [
        'cash' => 'Cash',
        'momo' => 'Mobile Money (MoMo)',
        'bank' => 'Bank Transfer',
        'card' => 'Card Payment',
    ];

    protected $casts = [
        'sale_date' => 'date',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

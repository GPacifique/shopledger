<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'sku', 'name', 'description', 'cost_price', 'sale_price', 'stock',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'sale_id', 'product_id', 'quantity', 'unit_price', 'cost_price_at_sale', 'line_total',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

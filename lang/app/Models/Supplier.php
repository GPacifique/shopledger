<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'name', 'contact_name', 'email', 'phone', 'address',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
   
}

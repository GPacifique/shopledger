<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}

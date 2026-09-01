<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Shop;
use App\Models\User;

class SystemUsageLog extends Model
{
    protected $fillable = [
        'id',
        'shop_id',
        'user_id',
        'action',
        'module',
        'ip_address',
        'device',
        'created_at',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
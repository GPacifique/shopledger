<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $table = 'subscriptionplans';

    protected $fillable = [
        'name',
        'price',
        'billing_cycle',
        'max_users',
        'max_products',
        'max_branches',
        'features',
        'status',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
    ];
}
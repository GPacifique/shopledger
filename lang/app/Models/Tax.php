<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'tax_name',
        'tax_rate',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tax_rate' => 'decimal:2',
    ];
}

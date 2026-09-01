<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'shop_id',
        'name',
        'description',
    ];

    /**
     * Category belongs to a shop
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Category has many expenses
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category_id');
    }
}
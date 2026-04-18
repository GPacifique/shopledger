<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'category_id',
        'amount',
        'expense_date',
        'reference',
        'description',
        'attachment',
        'created_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Expense belongs to a shop
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    // Expense belongs to a category
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    // Expense created by a user
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES (VERY USEFUL FOR REPORTS)
    |--------------------------------------------------------------------------
    */

    // Filter by shop
    public function scopeByShop($query, $shopId)
    {
        return $query->where('shop_id', $shopId);
    }

    // Filter current month
    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('expense_date', now()->month)
                     ->whereYear('expense_date', now()->year);
    }

    // Filter by date range
    public function scopeBetweenDates($query, $from, $to)
    {
        return $query->whereBetween('expense_date', [$from, $to]);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (OPTIONAL IMPROVEMENT)
    |--------------------------------------------------------------------------
    */

    // Format amount nicely
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtherIncome extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'income_category_id',
        'amount',
        'income_date',
        'reference',
        'description',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'income_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Shop this income belongs to.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Income category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(
            IncomeCategory::class,
            'income_category_id'
        );
    }

    /**
     * User who created the income record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'report_type',
        'is_enabled',
        'send_at',
        'day_of_week',
        'day_of_month',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'send_at' => 'datetime:H:i',
        'day_of_week' => 'integer',
        'day_of_month' => 'integer',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}

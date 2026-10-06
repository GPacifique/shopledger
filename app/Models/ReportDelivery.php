<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'report_type',
        'period_start',
        'period_end',
        'recipient_emails',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'sent_at' => 'datetime',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function getRecipientsAttribute(): array
    {
        if (empty($this->recipient_emails)) {
            return [];
        }

        return array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(',', $this->recipient_emails)
                )
            )
        );
    }
}

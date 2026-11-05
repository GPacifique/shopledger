<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionNotification extends Model
{
    protected $fillable = [
        'subscription_id',
        'type',
        'days_before',
        'channel',
        'notification_date',
        'sent_at',
    ];

    protected $casts = [
        'notification_date' => 'date',
        'sent_at' => 'datetime',
    ];
}

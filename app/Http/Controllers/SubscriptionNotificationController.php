<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionNotificationController extends Controller
{
    public function markRead(Request $request, UserNotification $notification): RedirectResponse
    {
        abort_unless(
            $notification->notifiable_type === $request->user()->getMorphClass()
                && (string) $notification->notifiable_id === (string) $request->user()->getKey(),
            403
        );

        $notification->markAsRead();

        return back();
    }
}

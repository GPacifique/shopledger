<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Notification;

abstract class Controller
{
    protected function notifyShopAdmins(int $shopId, \Illuminate\Notifications\Notification $notification, ?int $exceptUserId = null): void
    {
        $shopAdmins = User::where('shop_id', $shopId)
            ->where('role', 'shop_admin')
            ->when($exceptUserId, fn ($query) => $query->where('id', '!=', $exceptUserId))
            ->get();

        if ($shopAdmins->isNotEmpty()) {
            Notification::send($shopAdmins, $notification);
        }
    }
}

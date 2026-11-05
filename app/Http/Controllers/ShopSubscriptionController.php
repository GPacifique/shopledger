<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ShopSubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        abort_unless($user?->isShopAdmin() && $user->shop_id, 403);

        $shop = $user->shop;
        $subscription = $shop?->subscription()
            ->with(['subscriptionPlan', 'payments.recordedBy'])
            ->first();

        return view('shop.subscriptions.index', compact('shop', 'subscription'));
    }

    public function expired()
    {
        $user = Auth::user();
        abort_unless($user && $user->shop_id, 403);

        $shop = $user->shop;
        $subscription = $shop?->subscription?->load('subscriptionPlan');

        return view('subscriptions.expired', compact('shop', 'subscription', 'user'));
    }
}

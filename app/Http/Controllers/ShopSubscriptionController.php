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
        $ownerShops = $user->role === 'business_owner'
            ? $user->shops()
                ->where('status', 'approved')
                ->orderBy('business_name')
                ->get(['shops.id', 'shops.business_name'])
            : collect();

        if ($user->role === 'business_owner') {
            abort_unless($ownerShops->contains('id', $shop?->id), 403);
        }

        $subscription = $shop?->subscription()
            ->with(['subscriptionPlan', 'payments.recordedBy'])
            ->first();

        return view('shop.subscriptions.index', compact('shop', 'subscription', 'ownerShops'));
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

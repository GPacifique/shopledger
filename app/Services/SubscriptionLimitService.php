<?php

namespace App\Services;

use App\Models\Shop;
use Illuminate\Validation\ValidationException;

class SubscriptionLimitService
{
    public function assertCanAddUsers(Shop $shop, int $quantity = 1): void
    {
        $subscription = $shop->subscription()->with('subscriptionPlan')->first();
        $limit = $subscription?->subscriptionPlan?->max_users;

        if (! $subscription || ($limit !== null && $shop->users()->count() + $quantity > $limit)) {
            throw ValidationException::withMessages([
                'subscription' => __('Your subscription user limit has been reached. Upgrade your plan to add more staff.'),
            ]);
        }
    }

    public function assertCanAddProducts(Shop $shop, int $quantity = 1): void
    {
        $subscription = $shop->subscription()->with('subscriptionPlan')->first();
        $limit = $subscription?->subscriptionPlan?->max_products;

        if (! $subscription || ($limit !== null && $shop->products()->count() + $quantity > $limit)) {
            throw ValidationException::withMessages([
                'subscription' => __('Your subscription product limit has been reached. Upgrade your plan to add more products.'),
            ]);
        }
    }
}

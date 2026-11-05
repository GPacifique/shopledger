<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureShopSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            ! $user
            || $user->isSystemAdmin()
            || ! $user->shop_id
            || ($user->role === 'business_owner' && $request->routeIs('dashboard', 'business.dashboard'))
        ) {
            return $next($request);
        }

        if ($request->routeIs('shop.subscription.expired')) {
            return $next($request);
        }

        $subscription = $user->shop?->subscription;
        if (! $subscription) {
            return redirect()->route('shop.subscription.expired');
        }

        if (
            in_array($subscription->status, ['trial', 'active'], true)
            && $subscription->ends_at?->isPast()
        ) {
            $subscription->update([
                'status' => 'past_due',
                'grace_ends_at' => $subscription->ends_at->copy()->addDays(
                    (int) config('subscription.grace_days', 7)
                ),
            ]);
            $subscription->refresh();
        }

        if (
            $subscription->status === 'past_due'
            && $subscription->grace_ends_at?->isPast()
        ) {
            $subscription->update(['status' => 'expired']);
        }

        if (
            $subscription->isActive()
            || $subscription->onGracePeriod()
        ) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('Your shop subscription has expired. Contact your shop administrator.'),
            ], 402);
        }

        return redirect()->route('shop.subscription.expired');
    }
}

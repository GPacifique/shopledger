<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureShopIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // System administrators must be able to review pending shops.
        $role = strtolower(str_replace([' ', '-'], '_', (string) $user->role));

        if ($role === 'system_admin') {
            return $next($request);
        }

        if ($role === 'business_owner' && $request->routeIs('dashboard', 'business.dashboard')) {
            return $next($request);
        }

        if ($role === 'business_owner') {
            $shopId = $request->session()->get(
                'active_business_shop_id',
                $user->getRawOriginal('shop_id')
            );
            abort_unless(
                $shopId && $user->shops()->whereKey($shopId)->exists(),
                403
            );
        }

        // The user must be assigned to a shop.
        $shop = $user->shop;

        if (!$shop) {
    return redirect()->route('shop.assignment.required');
}

        // Only explicitly approved shops may access operational data.
        if (strtolower(trim((string) $shop->status)) !== 'approved') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your shop has not been approved by the System Administrator.',
                    'shop_status' => $shop->status,
                ], 403);
            }

            return response()->view('shops.pending-approval', [
                'shop' => $shop,
            ], 403);
        }

        return $next($request);
    }
}

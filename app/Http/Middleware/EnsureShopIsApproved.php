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

        // The user must be assigned to a shop.
        $shop = $user->shop;

        if (!$shop) {
            abort(403, 'Your account is not assigned to a shop.');
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

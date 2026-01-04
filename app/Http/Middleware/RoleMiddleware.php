<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware(RoleMiddleware::class.':shop_admin,manager')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        // System admin bypasses all checks
        if ($user->isSystemAdmin()) {
            return $next($request);
        }

        if (empty($roles)) {
            return $next($request);
        }

        if (!in_array($user->role, $roles, true)) {
            // Redirect to appropriate dashboard instead of 403
            return $this->redirectToDashboard($user);
        }

        return $next($request);
    }

    /**
     * Redirect user to their appropriate dashboard
     */
    protected function redirectToDashboard($user): Response
    {
        $route = match ($user->role) {
            'system_admin' => 'admin.dashboard',
            'shop_admin' => 'shop.dashboard',
            'seller' => 'seller.dashboard',
            'accountant' => 'accountant.dashboard',
            default => 'dashboard',
        };

        return redirect()->route($route)->with('error', 'You do not have access to that page.');
    }
}

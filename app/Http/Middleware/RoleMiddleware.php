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
            abort(403, 'Unauthorized role');
        }

        return $next($request);
    }
}

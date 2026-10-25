<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware(RoleMiddleware::class.':shop_admin,manager')
     *    or: ->middleware('role:shop_admin,manager')   (if aliased)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return $this->unauthenticated($request);
        }

        // Disabled accounts are signed out immediately (only if the column exists).
        if (array_key_exists('is_active', $user->getAttributes()) && ! $user->is_active) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => __('Your account is disabled.')]);
        }

        $role = $this->normalize($user->role);

        // System admin bypasses role checks (tenant data is still limited by the shop scope).
        if ($role === 'system_admin') {
            return $next($request);
        }

        // Fail closed: a route that forgot to list roles is a mistake, not "everyone".
        if (empty($roles)) {
            Log::error('RoleMiddleware used without roles', ['url' => $request->fullUrl()]);
            abort(403);
        }

        $allowed = array_map(fn ($r) => $this->normalize($r), $roles);

        if ($role === '' || ! in_array($role, $allowed, true)) {
            Log::warning('Role check failed', [
                'user_id' => $user->id,
                'role'    => $role,
                'needed'  => $allowed,
                'url'     => $request->fullUrl(),
                'method'  => $request->method(),
                'ip'      => $request->ip(),
            ]);

            return $this->denied($request, $role);
        }

        return $next($request);
    }

    protected function normalize($role): string
    {
        return strtolower(trim((string) $role));
    }

    protected function unauthenticated(Request $request): Response
    {
        if ($request->expectsJson()) {
            abort(401);
        }

        return redirect()->guest(route('login'));
    }

    /**
     * Friendly redirect to the user's own dashboard for normal page visits;
     * a plain 403 for API/AJAX calls, form submissions, and if a redirect would loop.
     */
    protected function denied(Request $request, string $role): Response
    {
        if ($request->expectsJson() || ! $request->isMethod('GET')) {
            abort(403, __('You do not have access to that page.'));
        }

        $route = $this->dashboardRouteFor($role);

        // Never redirect to the page we are already refusing (infinite loop).
        if ($route === null || $request->routeIs($route)) {
            abort(403, __('You do not have access to that page.'));
        }

        return redirect()->route($route)->with('error', __('You do not have access to that page.'));
    }

    /**
     * First dashboard route that really exists for this role.
     */
    protected function dashboardRouteFor(string $role): ?string
    {
        $candidates = match ($role) {
            'system_admin' => ['system-admin.dashboard', 'dashboard'],
            'shop_admin',
            'admin'        => ['shop.dashboard', 'dashboard'],
            'seller',
            'manager'      => ['seller.dashboard', 'dashboard'],
            'accountant'   => ['accountant.dashboard', 'dashboard'],
            'waiter'       => ['waiter.dashboard', 'shops.waiter.dashboard', 'dashboard'],
            default        => ['dashboard'],
        };

        foreach ($candidates as $name) {
            if (Route::has($name)) {
                return $name;
            }
        }

        return null;
    }
}
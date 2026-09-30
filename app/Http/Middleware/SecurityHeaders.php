<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds security headers to every response.
 *
 * File location: app/Http/Middleware/SecurityHeaders.php
 *
 * The Content-Security-Policy is controlled by CSP_MODE (see config/security.php):
 *   off     - no CSP header
 *   report  - Content-Security-Policy-Report-Only (logs violations in the browser
 *             console, blocks nothing). Start here.
 *   enforce - Content-Security-Policy (blocks violations)
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Must run before the view renders. Laravel's @vite tags pick this nonce up
        // automatically; inline scripts use nonce="{{ Vite::cspNonce() }}".
        $nonce = Vite::useCspNonce();

        /** @var Response $response */
        $response = $next($request);

        $headers = $response->headers;

        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        // Turn off powerful browser features the app does not use.
        // If you add barcode scanning with the camera, change camera=() to camera=(self).
        $headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=(), bluetooth=(), serial=()'
        );

        // HSTS only makes sense over HTTPS in production.
        if ($request->isSecure() && app()->environment('production')) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        $headers->remove('X-Powered-By');

        $mode = config('security.csp_mode', 'report');

        if ($mode === 'enforce') {
            $headers->set('Content-Security-Policy', $this->policy($nonce));
        } elseif ($mode === 'report') {
            $headers->set('Content-Security-Policy-Report-Only', $this->policy($nonce));
        }

        return $response;
    }

    private function policy(?string $nonce): string
    {
        // Alpine.js (standard build) evaluates the expressions in x-data / @click with
        // new Function(), which needs 'unsafe-eval'. Inline <script> blocks need the nonce.
        $script  = ["'self'", "'nonce-{$nonce}'", "'unsafe-eval'"];

        // The layout and sidebar use inline <style> blocks and style="" attributes.
        $style   = ["'self'", "'unsafe-inline'", 'https://fonts.googleapis.com'];

        $font    = ["'self'", 'data:', 'https://fonts.gstatic.com'];
        $img     = ["'self'", 'data:', 'blob:'];
        $connect = ["'self'"];

        // Allow the Vite dev server while `npm run dev` is running (public/hot exists).
        if (is_file($hot = public_path('hot'))) {
            $origin = rtrim(trim((string) file_get_contents($hot)), '/');
            $ws     = preg_replace('/^http/', 'ws', $origin);

            $script[]  = $origin;
            $style[]   = $origin;
            $font[]    = $origin;
            $img[]     = $origin;
            $connect[] = $origin;
            $connect[] = $ws;
        }

        $directives = [
            "default-src 'self'",
            'script-src '  . implode(' ', $script),
            'style-src '   . implode(' ', $style),
            'font-src '    . implode(' ', $font),
            'img-src '     . implode(' ', $img),
            'connect-src ' . implode(' ', $connect),
            "manifest-src 'self'",
            "worker-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ];

        if (app()->environment('production')) {
            $directives[] = 'upgrade-insecure-requests';
        }

        return implode('; ', $directives);
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Adds security headers to every response.
 *
 * File location: app/Http/Middleware/SecurityHeaders.php
 *
 * CSP is controlled by CSP_MODE (see config/security.php):
 *   off     - no CSP header
 *   report  - Content-Security-Policy-Report-Only (logs violations, blocks nothing)
 *   enforce - Content-Security-Policy (blocks violations)
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Must run before the view renders. @vite tags pick this nonce up
        // automatically; inline scripts use nonce="{{ Vite::cspNonce() }}".
        $nonce = Vite::useCspNonce();

        /** @var Response $response */
        $response = $next($request);

        $headers = $response->headers;

        // --- Content sniffing, framing, referrer ---------------------------
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        // Legacy headers. Modern browsers ignore them, but scanners look for them.
        $headers->set('X-XSS-Protection', '1; mode=block');
        $headers->set('X-Download-Options', 'noopen');
        $headers->set('X-DNS-Prefetch-Control', 'off');

        // --- Cross-origin isolation ----------------------------------------
        $headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        $headers->set('Cross-Origin-Embedder-Policy', 'credentialless');
        $headers->set('Origin-Agent-Cluster', '?1');

        // --- Browser features the app does not use -------------------------
        // If you add barcode scanning with the camera, change camera=() to camera=(self).
        $headers->set('Permissions-Policy', implode(', ', [
            'accelerometer=()',
            'autoplay=()',
            'bluetooth=()',
            'camera=()',
            'display-capture=()',
            'encrypted-media=()',
            'geolocation=()',
            'gyroscope=()',
            'hid=()',
            'magnetometer=()',
            'microphone=()',
            'midi=()',
            'payment=()',
            'screen-wake-lock=()',
            'serial=()',
            'usb=()',
            'xr-spatial-tracking=()',
        ]));

        // --- HSTS (HTTPS in production only) -------------------------------
        if ($request->isSecure() && app()->environment('production')) {
            $hsts = 'max-age=63072000; includeSubDomains';

            if (config('security.hsts_preload', true)) {
                $hsts .= '; preload';
            }

            $headers->set('Strict-Transport-Security', $hsts);
        }

        // --- Don't let browsers/proxies cache logged-in pages --------------
        if (
            $request->user()
            && ! $response instanceof BinaryFileResponse
            && ! $response instanceof StreamedResponse
        ) {
            $headers->set('Cache-Control', 'no-store, private');
            $headers->set('Pragma', 'no-cache');
        }

        // --- Hide server fingerprints --------------------------------------
        $headers->remove('X-Powered-By');
        if (! headers_sent()) {
            // PHP adds X-Powered-By itself, outside Laravel's response object.
            header_remove('X-Powered-By');
        }

        // --- Content-Security-Policy ---------------------------------------
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
        // Alpine.js (standard build) evaluates x-data / @click expressions with
        // new Function(), which needs 'unsafe-eval'. Switch to @alpinejs/csp to drop it.
        $script  = ["'self'", "'nonce-{$nonce}'", "'unsafe-eval'", 'https://cdn.jsdelivr.net'];

        // The layout and sidebar use inline <style> blocks and style="" attributes.
        $style   = ["'self'", "'unsafe-inline'", 'https://fonts.googleapis.com', 'https://fonts.bunny.net', 'https://cdnjs.cloudflare.com'];
        $font    = ["'self'", 'data:', 'https://fonts.gstatic.com', 'https://fonts.bunny.net', 'https://cdnjs.cloudflare.com'];
        $img     = ["'self'", 'data:', 'blob:'];
        $connect = ["'self'"];

        // Allow the Vite dev server while `npm run dev` is running (never in production).
        if (! app()->environment('production') && is_file($hot = public_path('hot'))) {
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
            "frame-src 'none'",
            "media-src 'self'",
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

        // Optional: collect violation reports at a URL you control.
        if ($uri = config('security.csp_report_uri')) {
            $directives[] = 'report-uri ' . $uri;
        }

        return implode('; ', $directives);
    }
}
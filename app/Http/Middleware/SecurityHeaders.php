<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = base64_encode(random_bytes(16));
        $request->attributes->set('csp_nonce', $nonce);
        Vite::useCspNonce($nonce);
        $response = $next($request);

        // Basic security headers
        foreach (config('security.headers') as $header => $value) {
            $response->headers->set($header, $value);
        }

        // Content Security Policy
        $cspDirectives = app()->environment('local')
            ? config('security.csp.development')
            : config('security.csp.production');

        if (! app()->environment('local')) {
            $cspDirectives['script-src'] .= " 'nonce-{$nonce}'";
        }

        $csp = collect($cspDirectives)
            ->map(fn ($value, $directive) => "{$directive} {$value}")
            ->implode('; ');

        $response->headers->set('Content-Security-Policy', $csp);

        if ($request->is('secrets/*')) {
            $response->headers->set('Cache-Control', 'no-store, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('X-Robots-Tag', 'noindex, noarchive');
        }

        // HSTS header (production only with HTTPS)
        if (app()->isProduction() && $request->isSecure()) {
            $hsts = 'max-age='.config('security.hsts.max_age');

            if (config('security.hsts.include_subdomains')) {
                $hsts .= '; includeSubDomains';
            }

            if (config('security.hsts.preload')) {
                $hsts .= '; preload';
            }

            $response->headers->set('Strict-Transport-Security', $hsts);
        }

        return $response;
    }
}

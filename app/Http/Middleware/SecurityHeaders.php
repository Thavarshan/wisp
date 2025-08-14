<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Basic security headers
        foreach (config('security.headers') as $header => $value) {
            $response->headers->set($header, $value);
        }

        // Content Security Policy
        $cspDirectives = app()->environment('local')
            ? config('security.csp.development')
            : config('security.csp.production');

        $csp = collect($cspDirectives)
            ->map(fn ($value, $directive) => "{$directive} {$value}")
            ->implode('; ');

        $response->headers->set('Content-Security-Policy', $csp);

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

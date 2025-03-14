<?php

namespace App\Http\Middleware;

use App\Models\Secret;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureSecret
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = $request->route('secret') ?? Secret::findByUid($request->query('secret'));

        if (! $secret) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if ($secret->hasExpired()) {
            abort(Response::HTTP_GONE, 'Secret has expired.');
        }

        return $next($request);
    }
}

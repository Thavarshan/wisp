<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockWebRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->meetsConditions($request)) {
            return $next($request);
        }

        return response()->json(
            'Web requests are not allowed on API routes.',
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * Determine if the request meets the conditions to consume the API.
     */
    protected function meetsConditions(Request $request): bool
    {
        return $request->isJson() && $request->wantsJson();
    }
}

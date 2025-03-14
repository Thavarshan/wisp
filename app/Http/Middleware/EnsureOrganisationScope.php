<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganisationScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $client = $request->user()->token()->client;

        if ($client->organisation_id !== $request->user()->organisation_id) {
            abort(
                Response::HTTP_UNAUTHORIZED,
                'Unauthorised access to this organisation.'
            );
        }

        return $next($request);
    }
}

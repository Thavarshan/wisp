<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateJsonStructure
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->shouldValidateJson($request)) {
            return $next($request);
        }

        if ($this->isInvalidJson($request->getContent())) {
            return response()->json(
                ['error' => 'Invalid JSON'],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $next($request);
    }

    /**
     * Determine if the request JSON should be validated.
     */
    protected function shouldValidateJson(Request $request): bool
    {
        return $request->expectsJson()
            && ! $request->isMethod('get')
            && ! blank($request->getContent());
    }

    /**
     * Check if the given JSON content is invalid.
     */
    protected function isInvalidJson(string $content): bool
    {
        json_decode($content);

        return json_last_error() !== \JSON_ERROR_NONE;
    }
}

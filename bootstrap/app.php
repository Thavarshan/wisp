<?php

use App\Enums\App;
use App\Http\Middleware\BlockWebRequests;
use App\Http\Middleware\EnsureOrganisationScope;
use App\Http\Middleware\ValidateJsonStructure;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;
use Spatie\Multitenancy\Http\Middleware\EnsureValidTenantSession;
use Spatie\Multitenancy\Http\Middleware\NeedsTenant;

$apiVersion = App::API_VERSION->value;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web/routes.php',
        api: __DIR__.sprintf('/../routes/api/%s/routes.php', $apiVersion),
        commands: __DIR__.'/../routes/console/routes.php',
        health: '/up',
        apiPrefix: $apiVersion,
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api([
            BlockWebRequests::class,
            ValidateJsonStructure::class,
        ]);

        $middleware->web([
            EnsureValidTenantSession::class,
        ]);

        $middleware->alias([
            'tenant' => NeedsTenant::class,
            'client' => CheckClientCredentials::class,
            'organisation.scope' => EnsureOrganisationScope::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

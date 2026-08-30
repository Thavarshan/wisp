<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SecurityHeaders;
use App\Models\Secret;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware is configured before the config repository is booted.
        $middleware->trustProxies(at: env('TRUSTED_PROXIES'));
        $middleware->trustHosts(
            at: fn (): array => ($host = parse_url(config('app.url'), PHP_URL_HOST))
                ? ['^'.preg_quote($host, '/').'$']
                : [],
            subdomains: false,
        );
        $middleware->encryptCookies();
        $middleware->preventRequestForgery(originOnly: true);

        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            SecurityHeaders::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function ($response) {
            if (request()->is('secrets/*')) {
                $response->headers->set('Cache-Control', 'no-store, private');
                $response->headers->set('Pragma', 'no-cache');
                $response->headers->set('X-Robots-Tag', 'noindex, noarchive');
            }

            return $response;
        });
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('model:prune', ['--model' => [Secret::class]])
            ->hourly()
            ->withoutOverlapping()
            ->onOneServer();
    })
    ->create();

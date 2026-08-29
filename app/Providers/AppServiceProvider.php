<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('secret-creation', fn (Request $request) => Limit::perMinute(10)->by('ip:'.$request->ip()));
        RateLimiter::for('secret-access', fn (Request $request) => Limit::perMinute(60)->by('ip:'.$request->ip()));
        RateLimiter::for('secret-reveal-ip', fn (Request $request) => Limit::perMinute(10)->by('ip:'.$request->ip()));
        RateLimiter::for('secret-reveal-secret', fn (Request $request) => Limit::perMinute(10)->by('token:'.hash('sha256', (string) $request->route('token')))
        );
        RateLimiter::for('secret-revocation', fn (Request $request) => [
            Limit::perMinute(20)->by('ip:'.$request->ip()),
            Limit::perMinute(20)->by('token:'.hash('sha256', (string) $request->route('token'))),
        ]);

        if (App::isProduction()) {
            URL::forceScheme('https');
        }
    }
}

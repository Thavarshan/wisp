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
    #[\Override]
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('secret-creation', function (Request $request) {
            return Limit::perMinute(10)->by('ip:'.$request->ip());
        });
        RateLimiter::for('secret-access', function (Request $request) {
            return Limit::perMinute(60)->by('ip:'.$request->ip());
        });
        RateLimiter::for('secret-reveal-ip', function (Request $request) {
            return Limit::perMinute(10)->by('ip:'.$request->ip());
        });
        RateLimiter::for('secret-reveal-secret', function (Request $request) {
            $tokenHash = hash(
                'sha256',
                (string) $request->route('token'),
            );

            return Limit::perMinute(10)->by('token:'.$tokenHash);
        });
        RateLimiter::for('secret-revocation', function (Request $request) {
            $tokenHash = hash(
                'sha256',
                (string) $request->route('token'),
            );

            return [
                Limit::perMinute(20)->by('ip:'.$request->ip()),
                Limit::perMinute(20)->by('token:'.$tokenHash),
            ];
        });

        if (App::isProduction()) {
            URL::forceScheme('https');
        }
    }
}

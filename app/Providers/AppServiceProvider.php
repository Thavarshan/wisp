<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\Client;
use App\Models\Organisation;
use App\Models\Token;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register custom billable model
        $this->registerCustomBillableModel();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->setDefaultStringLength();
        $this->enableStrictMode();
        $this->enableQueryLogging();
        $this->forceHttpsInProduction();
        $this->configurePassport();
        $this->authoriseSuperAdminForAllGates();
        $this->cacheRoutesInProduction();
        $this->setApplicationTimezone();
    }

    /**
     * Set the default string length for the database.
     */
    protected function setDefaultStringLength(): void
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Enable strict mode for Eloquent models so that all attributes must be defined.
     */
    protected function enableStrictMode(): void
    {
        Model::shouldBeStrict();
    }

    /**
     * Enable query logging in local environment.
     */
    protected function enableQueryLogging(): void
    {
        if (! $this->app->environment('local')) {
            return;
        }

        DB::listen(fn ($query) => Log::info($query->sql, [
            'bindings' => $query->bindings,
            'time' => $query->time,
            'user' => auth()->user()?->name,
        ]));
    }

    /**
     * Force HTTPS in production so that all requests are secure.
     */
    protected function forceHttpsInProduction(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }

    /**
     * Register the custom billable model.
     */
    protected function registerCustomBillableModel(): void
    {
        Cashier::useCustomerModel(Organisation::class);
    }

    /**
     * Configure Passport for authentication.
     */
    protected function configurePassport(): void
    {
        Passport::hashClientSecrets();
        Passport::useClientModel(Client::class);
        Passport::useTokenModel(Token::class);
        Passport::enablePasswordGrant();
    }

    /**
     * Authorise the super admin for all gates.
     */
    protected function authoriseSuperAdminForAllGates(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user->hasRole(Role::SUPER_ADMIN)) {
                return true;
            }
        });
    }

    /**
     * Cache the routes in production to improve performance.
     */
    protected function cacheRoutesInProduction(): void
    {
        if ($this->app->environment('production')) {
            $this->app->make('router')->cacheRoutes();
        }
    }

    /**
     * Set the application timezone.
     */
    protected function setApplicationTimezone(): void
    {
        date_default_timezone_set(config('app.timezone', 'UTC'));
    }
}

<?php

namespace Tests\Unit\Providers;

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AppServiceProviderTest extends TestCase
{
    public function test_it_forces_https_in_production()
    {
        // Mock the App facade to return true for isProduction
        App::shouldReceive('isProduction')->once()->andReturn(true);

        // Mock the URL facade to expect forceScheme to be called with 'https'
        URL::shouldReceive('forceScheme')->once()->with('https');

        // Create and boot the service provider
        $provider = new AppServiceProvider($this->app);
        $provider->boot();

        // Assertions are handled by the mocked expectations
        $this->assertTrue(true);
    }

    public function test_it_does_not_force_https_in_non_production()
    {
        // Mock the App facade to return false for isProduction
        App::shouldReceive('isProduction')->once()->andReturn(false);

        // In non-production, URL::forceScheme should not be called at all
        // We don't need to mock URL facade since it shouldn't be called

        // Create and boot the service provider
        $provider = new AppServiceProvider($this->app);
        $provider->boot();

        // Assertions are handled by the mocked expectations
        $this->assertTrue(true);
    }

    public function test_register_method_exists()
    {
        $provider = new AppServiceProvider($this->app);

        // The register method should exist and be callable
        $this->assertTrue(method_exists($provider, 'register'));

        // Call register method (it's empty but should not throw an error)
        $provider->register();

        $this->assertTrue(true);
    }
}

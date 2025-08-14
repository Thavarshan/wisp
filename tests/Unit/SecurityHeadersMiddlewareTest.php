<?php

namespace Tests\Unit;

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class SecurityHeadersMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected SecurityHeaders $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new SecurityHeaders;
    }

    public function test_it_adds_basic_security_headers(): void
    {
        $request = Request::create('/', 'GET');

        $response = $this->middleware->handle($request, function () {
            return new Response('Test content');
        });

        // Test basic security headers
        $this->assertEquals('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertEquals('DENY', $response->headers->get('X-Frame-Options'));
        $this->assertEquals('strict-origin-when-cross-origin', $response->headers->get('Referrer-Policy'));
    }

    public function test_it_adds_development_csp_in_local_environment(): void
    {
        // Force local environment
        $this->app['env'] = 'local';

        $request = Request::create('/', 'GET');

        $response = $this->middleware->handle($request, function () {
            return new Response('Test content');
        });

        $csp = $response->headers->get('Content-Security-Policy');

        // Should contain development-specific directives
        $this->assertStringContainsString('unsafe-inline', $csp);
        $this->assertStringContainsString('unsafe-eval', $csp);
        $this->assertStringContainsString('localhost:*', $csp);
        $this->assertStringContainsString('127.0.0.1:*', $csp);
    }

    public function test_it_adds_production_csp_in_production_environment(): void
    {
        // Force production environment
        $this->app['env'] = 'production';

        $request = Request::create('/', 'GET');

        $response = $this->middleware->handle($request, function () {
            return new Response('Test content');
        });

        $csp = $response->headers->get('Content-Security-Policy');

        // Should NOT contain development-specific script directives
        $this->assertStringNotContainsString('unsafe-eval', $csp);
        $this->assertStringNotContainsString('localhost:*', $csp);
        $this->assertStringNotContainsString('127.0.0.1:*', $csp);

        // Should contain strict production script directives
        $this->assertStringContainsString("script-src 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);

        // Should still allow unsafe-inline for styles (needed for Tailwind)
        $this->assertStringContainsString("style-src 'self' 'unsafe-inline'", $csp);
    }

    public function test_it_does_not_add_hsts_in_local_environment(): void
    {
        // Force local environment
        $this->app['env'] = 'local';

        $request = Request::create('/', 'GET');

        $response = $this->middleware->handle($request, function () {
            return new Response('Test content');
        });

        $this->assertNull($response->headers->get('Strict-Transport-Security'));
    }

    public function test_it_does_not_add_hsts_in_production_without_https(): void
    {
        // Force production environment
        $this->app['env'] = 'production';

        // HTTP request (not secure)
        $request = Request::create('http://example.com/', 'GET');

        $response = $this->middleware->handle($request, function () {
            return new Response('Test content');
        });

        $this->assertNull($response->headers->get('Strict-Transport-Security'));
    }

    public function test_it_adds_hsts_in_production_with_https(): void
    {
        // Force production environment
        $this->app['env'] = 'production';

        // HTTPS request
        $request = Request::create('https://example.com/', 'GET');

        $response = $this->middleware->handle($request, function () {
            return new Response('Test content');
        });

        $hsts = $response->headers->get('Strict-Transport-Security');
        $this->assertNotNull($hsts);
        $this->assertStringContainsString('max-age=31536000', $hsts);
        $this->assertStringContainsString('includeSubDomains', $hsts);
    }

    public function test_it_adds_hsts_with_preload_when_enabled(): void
    {
        // Force production environment
        $this->app['env'] = 'production';
        config(['security.hsts.preload' => true]);

        // HTTPS request
        $request = Request::create('https://example.com/', 'GET');

        $response = $this->middleware->handle($request, function () {
            return new Response('Test content');
        });

        $hsts = $response->headers->get('Strict-Transport-Security');
        $this->assertNotNull($hsts);
        $this->assertStringContainsString('preload', $hsts);
    }

    public function test_it_preserves_original_response_content(): void
    {
        $request = Request::create('/', 'GET');
        $originalContent = 'Original response content';

        $response = $this->middleware->handle($request, function () use ($originalContent) {
            return new Response($originalContent);
        });

        $this->assertEquals($originalContent, $response->getContent());
    }

    public function test_csp_includes_fonts_bunny_in_all_environments(): void
    {
        // Test local environment
        $this->app['env'] = 'local';
        $request = Request::create('/', 'GET');

        $response = $this->middleware->handle($request, function () {
            return new Response('Test');
        });

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString('https://fonts.bunny.net', $csp);

        // Test production environment
        $this->app['env'] = 'production';

        $response = $this->middleware->handle($request, function () {
            return new Response('Test');
        });

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString('https://fonts.bunny.net', $csp);
    }
}

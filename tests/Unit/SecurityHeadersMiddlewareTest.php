<?php

namespace Tests\Unit;

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class SecurityHeadersMiddlewareTest extends TestCase
{
    public function test_it_adds_the_production_security_policy(): void
    {
        $this->app['env'] = 'production';
        $response = (new SecurityHeaders)->handle(Request::create('/', 'GET'), fn () => new Response('content'));
        $csp = $response->headers->get('Content-Security-Policy');

        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertSame('DENY', $response->headers->get('X-Frame-Options'));
        $this->assertSame('no-referrer', $response->headers->get('Referrer-Policy'));
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("base-uri 'self'", $csp);
        $this->assertStringNotContainsString("style-src 'unsafe-inline'", $csp);
        $this->assertStringContainsString("style-src-attr 'unsafe-inline'", $csp);
        $this->assertMatchesRegularExpression("/script-src 'self' 'nonce-[^']+'/", $csp);
    }

    public function test_it_adds_hsts_only_for_secure_production_requests(): void
    {
        $this->app['env'] = 'production';
        $https = (new SecurityHeaders)->handle(Request::create('https://example.com/', 'GET'), fn () => new Response);
        $http = (new SecurityHeaders)->handle(Request::create('http://example.com/', 'GET'), fn () => new Response);

        $this->assertStringContainsString('max-age=31536000', $https->headers->get('Strict-Transport-Security'));
        $this->assertNull($http->headers->get('Strict-Transport-Security'));
    }

    public function test_secret_responses_are_marked_private_and_not_indexable(): void
    {
        $response = (new SecurityHeaders)->handle(Request::create('/secrets/token', 'GET'), fn () => new Response);

        $this->assertSame('no-store, private', $response->headers->get('Cache-Control'));
        $this->assertSame('noindex, noarchive', $response->headers->get('X-Robots-Tag'));
    }
}

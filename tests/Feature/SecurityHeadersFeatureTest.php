<?php

namespace Tests\Feature;

use App\Actions\CreateSecret;
use App\Enums\ExpirationOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_pages_include_security_headers(): void
    {
        $response = $this->get('/');
        $response->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'no-referrer')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
        $this->assertStringContainsString("form-action 'self'", $csp);
        preg_match("/script-src 'self' 'nonce-([^']+)'/", $csp, $matches);
        $this->assertNotEmpty($matches[1] ?? null);
        $this->assertStringContainsString('nonce="'.$matches[1].'"', $response->getContent());
    }

    public function test_secret_pages_are_not_cacheable_or_indexable(): void
    {
        $created = app(CreateSecret::class)->handle(['content' => 'header test', 'expiration' => ExpirationOption::ONE_DAY->value, 'password' => null]);
        $this->get(route('secrets.show', ['secret_id' => $created['secret_id']]))
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertHeader('X-Robots-Tag', 'noindex, noarchive');
    }
}

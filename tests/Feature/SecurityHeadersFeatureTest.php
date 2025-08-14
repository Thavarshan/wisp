<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_includes_security_headers(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Assert basic security headers are present
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Assert CSP header is present
        $this->assertNotNull($response->headers->get('Content-Security-Policy'));
    }

    public function test_secret_routes_include_security_headers(): void
    {
        // Create a secret for testing
        $secret = \App\Models\Secret::factory()->create();

        $response = $this->get(route('secrets.show', $secret));

        $response->assertStatus(200);

        // Assert security headers are present on secret routes
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Assert CSP header is present
        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertNotNull($csp);

        // In testing environment, should include development CSP
        $this->assertStringContainsString('unsafe-inline', $csp);
    }

    public function test_api_routes_include_security_headers(): void
    {
        $response = $this->postJson('/secrets', [
            'content' => 'Test secret content',
            'expired_at' => \App\Enums\ExpirationOption::ONE_DAY->value,
        ]);

        // Should redirect after creation
        $response->assertStatus(302);

        // Assert security headers are present on API routes
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_csp_allows_required_resources(): void
    {
        $response = $this->get('/');

        $csp = $response->headers->get('Content-Security-Policy');

        // Should allow self
        $this->assertStringContainsString("'self'", $csp);

        // Should allow Google Fonts
        $this->assertStringContainsString('https://fonts.bunny.net', $csp);

        // Should allow data URIs for images
        $this->assertStringContainsString('data:', $csp);
    }

    public function test_no_hsts_header_in_testing_environment(): void
    {
        $response = $this->get('/');

        // HSTS should not be present in testing environment
        $this->assertNull($response->headers->get('Strict-Transport-Security'));
    }
}

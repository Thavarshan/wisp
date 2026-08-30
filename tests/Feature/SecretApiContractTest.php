<?php

namespace Tests\Feature;

use App\Actions\CreateSecret;
use App\Enums\ExpirationOption;
use App\Models\Secret;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SecretApiContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_validates_domain_constraints_with_json_errors(): void
    {
        $response = $this->postJson(route('secrets.store'), [
            'content' => str_repeat('x', Secret::MAX_CONTENT_LENGTH + 1),
            'expiration' => 'invalid',
            'password' => str_repeat('x', Secret::MAX_PASSWORD_BYTES + 1),
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['content', 'expiration', 'password'])
            ->assertJsonPath('errors.content.0', 'Secret content may not exceed 10000 characters.')
            ->assertJsonPath('errors.expiration.0', 'Choose a valid expiration option.')
            ->assertJsonPath('errors.password.0', 'The password may not exceed 72 UTF-8 bytes and may not contain null characters.');
    }

    public function test_store_returns_an_explicit_no_store_contract(): void
    {
        $response = $this->postJson(route('secrets.store'), [
            'content' => 'private content',
            'expiration' => ExpirationOption::ONE_HOUR->value,
            'password' => null,
        ]);

        $response->assertCreated()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertHeader('X-Robots-Tag', 'noindex, noarchive')
            ->assertJsonStructure([
                'secret_id',
                'revocation_token',
                'share_url',
                'expires_at',
                'expiration' => ['value', 'label'],
            ]);
    }

    public function test_json_page_failures_are_predictable(): void
    {
        $this->getJson(route('secrets.show', ['secret_id' => str_repeat('a', 64)]))
            ->assertNotFound()
            ->assertExactJson(['message' => 'This secret is no longer available.']);

        $created = app(CreateSecret::class)->handle([
            'content' => 'expired content',
            'expiration' => ExpirationOption::ONE_DAY->value,
            'password' => null,
        ]);
        Secret::query()->withSecretId($created['secret_id'])->firstOrFail()->update([
            'expired_at' => now()->subSecond(),
        ]);

        $this->getJson(route('secrets.show', ['secret_id' => $created['secret_id']]))
            ->assertGone()
            ->assertExactJson(['message' => 'This secret has expired.']);
    }

    public function test_reveal_hides_missing_malformed_and_mismatched_access_tokens(): void
    {
        $created = app(CreateSecret::class)->handle([
            'content' => 'protected by the fragment token',
            'expiration' => ExpirationOption::ONE_DAY->value,
            'password' => null,
        ]);

        $route = route('secrets.reveal', ['secret_id' => $created['secret_id']]);

        $this->postJson($route)->assertNotFound();
        $this->postJson($route, ['access_token' => 'invalid'])->assertNotFound();
        $this->postJson($route, ['access_token' => str_repeat('0', 64)])->assertNotFound();
        $this->postJson($route, ['access_token' => ['invalid']])->assertNotFound();
    }

    public function test_password_validation_uses_bcrypt_byte_boundaries(): void
    {
        $payload = [
            'content' => 'byte boundary',
            'expiration' => ExpirationOption::ONE_HOUR->value,
        ];

        $this->postJson(route('secrets.store'), $payload + [
            'password' => str_repeat('x', Secret::MAX_PASSWORD_BYTES),
        ])->assertCreated();

        $this->postJson(route('secrets.store'), $payload + [
            'password' => str_repeat('x', Secret::MAX_PASSWORD_BYTES + 1),
        ])->assertUnprocessable()->assertJsonValidationErrors('password');

        $this->postJson(route('secrets.store'), $payload + [
            'password' => str_repeat('😀', 18),
        ])->assertCreated();

        $this->postJson(route('secrets.store'), $payload + [
            'password' => str_repeat('😀', 19),
        ])->assertUnprocessable()->assertJsonValidationErrors('password');

        $this->postJson(route('secrets.store'), $payload + [
            'password' => "valid\0password",
        ])->assertUnprocessable()->assertJsonValidationErrors('password');
    }

    public function test_share_urls_use_the_configured_host_and_store_unicode_content(): void
    {
        $content = str_repeat('😀', Secret::MAX_CONTENT_LENGTH);
        $response = $this->withHeader('Host', 'attacker.example')->postJson(route('secrets.store'), [
            'content' => $content,
            'expiration' => ExpirationOption::ONE_HOUR->value,
            'password' => null,
        ]);

        $response->assertCreated();
        $shareUrl = $response->json('share_url');
        $this->assertStringStartsWith(rtrim(config('app.url'), '/').'/secrets/', $shareUrl);
        $this->assertSame($content, $this->postJson(
            route('secrets.reveal', ['secret_id' => $response->json('secret_id')]),
            ['access_token' => parse_url($shareUrl, PHP_URL_FRAGMENT)],
        )->json('content'));
    }

    public function test_pruning_removes_expired_secrets_without_touching_active_secrets(): void
    {
        $expired = Secret::factory()->create(['expired_at' => now()->subMinute()]);
        $active = Secret::factory()->create(['expired_at' => now()->addMinute()]);

        Artisan::call('model:prune', ['--model' => [Secret::class]]);

        $this->assertModelMissing($expired);
        $this->assertModelExists($active);
    }
}

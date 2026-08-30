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
            'password' => str_repeat('x', Secret::MAX_PASSWORD_LENGTH + 1),
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['content', 'expiration', 'password'])
            ->assertJsonPath('errors.content.0', 'Secret content may not exceed 10000 characters.')
            ->assertJsonPath('errors.expiration.0', 'Choose a valid expiration option.')
            ->assertJsonPath('errors.password.0', 'The password may not exceed 255 characters.');
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
                'access_token',
                'revocation_token',
                'share_url',
                'expires_at',
                'expiration' => ['value', 'label'],
            ]);
    }

    public function test_json_page_failures_are_predictable(): void
    {
        $this->getJson(route('secrets.show', ['token' => str_repeat('a', 64)]))
            ->assertNotFound()
            ->assertExactJson(['message' => 'This secret is no longer available.']);

        $created = app(CreateSecret::class)->handle([
            'content' => 'expired content',
            'expiration' => ExpirationOption::ONE_DAY->value,
            'password' => null,
        ]);
        Secret::query()->withAccessToken($created['access_token'])->firstOrFail()->update([
            'expired_at' => now()->subSecond(),
        ]);

        $this->getJson(route('secrets.show', ['token' => $created['access_token']]))
            ->assertGone()
            ->assertExactJson(['message' => 'This secret has expired.']);
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

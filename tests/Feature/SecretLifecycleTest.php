<?php

namespace Tests\Feature;

use App\Actions\CreateSecret;
use App\Enums\ExpirationOption;
use App\Models\Secret;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecretLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_creation_returns_raw_tokens_once_and_stores_only_hashes(): void
    {
        $response = $this->postJson(route('secrets.store'), [
            'content' => 'private message', 'expiration' => ExpirationOption::ONE_DAY->value, 'password' => 'correct horse',
        ]);
        $response->assertCreated()->assertJsonStructure(['access_token', 'revocation_token', 'share_url', 'expires_at', 'expiration' => ['value', 'label']]);

        $payload = $response->json();
        $row = DB::table('secrets')->first();
        $this->assertSame(64, strlen($payload['access_token']));
        $this->assertSame(64, strlen($payload['revocation_token']));
        $this->assertSame(Secret::hashToken($payload['access_token']), $row->access_token_hash);
        $this->assertSame(Secret::hashToken($payload['revocation_token']), $row->revocation_token_hash);
        $this->assertNotSame($payload['access_token'], $row->access_token_hash);
        $this->assertNotSame($payload['revocation_token'], $row->revocation_token_hash);
        $this->assertStringNotContainsString('private message', $row->content);
        $this->assertTrue(Hash::check('correct horse', $row->password));
    }

    public function test_initial_access_returns_metadata_but_never_plaintext(): void
    {
        $created = app(CreateSecret::class)->handle(['content' => 'do not serialize me', 'expiration' => ExpirationOption::ONE_DAY->value, 'password' => null]);
        $response = $this->get(route('secrets.show', ['token' => $created['access_token']]));

        $response->assertOk()->assertInertia(fn ($page) => $page->component('Secret')->where('token', $created['access_token'])->where('has_password', false)->missing('content'));
        $response->assertDontSee('do not serialize me');
        $this->assertDatabaseCount('secrets', 1);
    }

    public function test_wrong_password_does_not_consume_or_clear_the_password(): void
    {
        $created = app(CreateSecret::class)->handle(['content' => 'password protected', 'expiration' => ExpirationOption::ONE_DAY->value, 'password' => 'correct password']);
        $this->postJson(route('secrets.reveal', ['token' => $created['access_token']]), ['password' => 'wrong password'])->assertUnprocessable()->assertJsonValidationErrors('password');

        $secret = Secret::query()->withAccessToken($created['access_token'])->firstOrFail();
        $this->assertTrue(Hash::check('correct password', $secret->password));
    }

    public function test_reveal_is_one_time_and_only_the_first_request_succeeds(): void
    {
        $created = app(CreateSecret::class)->handle(['content' => 'one time value', 'expiration' => ExpirationOption::ONE_DAY->value, 'password' => null]);
        $first = $this->postJson(route('secrets.reveal', ['token' => $created['access_token']]));
        $second = $this->postJson(route('secrets.reveal', ['token' => $created['access_token']]));

        $first->assertOk()->assertExactJson(['content' => 'one time value']);
        $second->assertNotFound();
        $this->assertDatabaseCount('secrets', 0);
    }

    public function test_correct_password_reveals_and_deletes_the_secret(): void
    {
        $created = app(CreateSecret::class)->handle(['content' => 'protected value', 'expiration' => ExpirationOption::ONE_DAY->value, 'password' => 'correct password']);
        $response = $this->postJson(route('secrets.reveal', ['token' => $created['access_token']]), ['password' => 'correct password']);
        $response->assertOk()->assertJsonPath('content', 'protected value');
        $this->assertDatabaseCount('secrets', 0);
    }

    public function test_revocation_requires_the_separate_token(): void
    {
        $created = app(CreateSecret::class)->handle(['content' => 'revoke me', 'expiration' => ExpirationOption::ONE_DAY->value, 'password' => null]);
        $this->deleteJson(route('secrets.revoke', ['token' => $created['access_token']]), ['revocation_token' => str_repeat('0', 64)])->assertNotFound();
        $this->assertDatabaseCount('secrets', 1);
        $this->deleteJson(route('secrets.revoke', ['token' => $created['access_token']]), ['revocation_token' => $created['revocation_token']])->assertNoContent();
        $this->assertDatabaseCount('secrets', 0);
    }

    public function test_expired_secrets_are_not_revealed(): void
    {
        $created = app(CreateSecret::class)->handle(['content' => 'expired value', 'expiration' => ExpirationOption::FIVE_MINUTES->value, 'password' => null]);
        Secret::query()->withAccessToken($created['access_token'])->firstOrFail()->update(['expired_at' => now()->subSecond()]);
        $this->get(route('secrets.show', ['token' => $created['access_token']]))->assertGone();
        $this->postJson(route('secrets.reveal', ['token' => $created['access_token']]))->assertGone();
    }
}

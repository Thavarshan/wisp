<?php

namespace Tests\Feature;

use App\Enums\ExpirationOption;
use App\Models\Secret;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SecretControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_a_new_secret(): void
    {
        $response = $this->post(route('secrets.store'), [
            'content' => 'This is a secret',
            'expired_at' => ExpirationOption::ONE_DAY->value,
        ]);

        $response->assertRedirect(route('secrets.share', ['secret' => Secret::first()->uid]));
        $this->assertDatabaseHas('secrets', ['uid' => Secret::first()->uid]);
    }

    public function test_it_displays_a_secret(): void
    {
        $secret = Secret::create([
            'expired_at' => ExpirationOption::parse(ExpirationOption::ONE_DAY->value),
            'content' => Crypt::encrypt('This is a secret'),
        ]);

        $response = $this->get(route('secrets.show', $secret));

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Secret')
                ->where('secret', Crypt::decrypt($secret->content))
        );

        // Secret should still exist in the database until revealed
        $this->assertDatabaseHas('secrets', ['uid' => $secret->uid]);
    }

    public function test_it_destroys_a_secret(): void
    {
        $secret = Secret::create([
            'expired_at' => ExpirationOption::parse(ExpirationOption::ONE_DAY->value),
            'content' => Crypt::encrypt('This is a secret'),
        ]);

        $response = $this->deleteJson(route('secrets.destroy', $secret));

        $response->assertNoContent();  // Check for 204 No Content
        $this->assertDatabaseMissing('secrets', ['id' => $secret->id]);
    }

    public function test_it_displays_a_password_protected_secret(): void
    {
        $secret = Secret::create([
            'expired_at' => now()->addDay(),
            'content' => Crypt::encrypt('This is a secret'),
            'password' => 'password123', // is hashed during create event
        ]);

        $response = $this->postJson(route('secrets.password', $secret), [
            'password' => 'password123',
        ]);

        $response->assertNoContent();  // Expecting 204 No Content

        $response = $this->get(route('secrets.show', $secret));
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Secret')
                ->where('secret', Crypt::decrypt($secret->content))
        );

        // Secret should still exist until revealed
        $this->assertDatabaseHas('secrets', ['uid' => $secret->uid]);
    }

    public function test_it_requires_correct_password_for_secret(): void
    {
        $secret = Secret::create([
            'expired_at' => now()->addDay(),
            'content' => Crypt::encrypt('This is a secret'),
            'password' => 'password123',  // is hashed during create event
        ]);

        $response = $this->postJson(route('secrets.password', $secret), [
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422);  // Expecting 422 Unprocessable Entity
        $response->assertJsonValidationErrors('password');  // Check for validation error

        $this->assertDatabaseHas('secrets', ['uid' => $secret->uid]);
    }

    public function test_it_deletes_secret_after_reveal(): void
    {
        $secret = Secret::create([
            'expired_at' => now()->addDay(),
            'content' => Crypt::encrypt('This is a one-time secret'),
        ]);

        // Simulate revealing the secret by deleting it
        $response = $this->deleteJson(route('secrets.destroy', $secret));

        $response->assertNoContent();  // Expecting 204 No Content

        // Ensure the secret is deleted after being revealed
        $this->assertDatabaseMissing('secrets', ['uid' => $secret->uid]);

        // Try to view the secret again
        $response = $this->get(route('secrets.show', $secret));
        $response->assertNotFound();  // Expecting 404 Not Found
    }
}

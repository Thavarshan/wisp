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

        $this->assertDatabaseMissing('secrets', ['uid' => $secret->uid]);
    }

    public function test_it_destroys_a_secret(): void
    {
        $secret = Secret::create([
            'expired_at' => ExpirationOption::parse(ExpirationOption::ONE_DAY->value),
            'content' => Crypt::encrypt('This is a secret'),
        ]);

        $response = $this->delete(route('secrets.destroy', $secret));

        $response->assertRedirect(route('home'));
        $this->assertDatabaseMissing('secrets', ['id' => $secret->id]);
    }

    public function test_it_displays_a_password_protected_secret(): void
    {
        $secret = Secret::create([
            'expired_at' => now()->addDay(),
            'content' => Crypt::encrypt('This is a secret'),
            'password' => bcrypt('password123'),
        ]);

        // Simulate entering the correct password
        $response = $this->post(route('secrets.password', $secret), [
            'password' => 'password123',
        ]);

        // Follow the redirect to the secret
        $response = $this->get(route('secrets.show', $secret));

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Secret')
                ->where('secret', Crypt::decrypt($secret->content))
        );

        // Ensure the secret is deleted after being viewed
        $this->assertDatabaseMissing('secrets', ['uid' => $secret->uid]);
    }

    public function test_it_requires_correct_password_for_secret(): void
    {
        $secret = Secret::create([
            'expired_at' => now()->addDay(),
            'content' => Crypt::encrypt('This is a secret'),
            'password' => bcrypt('password123'),
        ]);

        // Attempt to access with an incorrect password
        $response = $this->post(route('secrets.password', $secret), [
            'password' => 'wrongpassword',
        ]);

        // Ensure the secret is still in the database
        $this->assertDatabaseHas('secrets', ['uid' => $secret->uid]);

        // Check for password error
        $response->assertSessionHasErrors('password');
    }

    public function test_it_deletes_secret_after_first_view(): void
    {
        $secret = Secret::create([
            'expired_at' => now()->addDay(),
            'content' => Crypt::encrypt('This is a one-time secret'),
        ]);

        // View the secret
        $this->get(route('secrets.show', $secret));

        // Ensure the secret is deleted after viewing
        $this->assertDatabaseMissing('secrets', ['uid' => $secret->uid]);

        // Try to view the secret again
        $response = $this->get(route('secrets.show', $secret));

        // Ensure a 404 response
        $response->assertNotFound();
    }
}

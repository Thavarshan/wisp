<?php

namespace Tests\Feature;

use App\Models\Secret;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CreateNewSecretTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_new_secret(): void
    {
        $response = $this->post(route('secrets.store'), [
            'content' => 'This is a secret',
            'password' => 'password123',
            'expired_at' => Carbon::now()->addDay(),
        ]);

        $response->assertRedirect(route('secrets.share', ['secret' => Secret::first()->uid]));
        $this->assertDatabaseHas('secrets', ['content' => Crypt::encrypt('This is a secret')]);
    }

    public function test_can_view_a_secret(): void
    {
        $secret = Secret::factory()->create(['content' => Crypt::encrypt('This is a secret')]);

        $response = $this->get(route('secrets.show', $secret->uid));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee('This is a secret');
    }

    public function test_handles_expired_secrets(): void
    {
        $secret = Secret::factory()->create([
            'content' => Crypt::encrypt('This is a secret'),
            'expired_at' => Carbon::now()->subDay(),
        ]);

        $response = $this->get(route('secrets.show', $secret->uid));

        $response->assertStatus(Response::HTTP_GONE);
        $response->assertSee('Secret has expired.');
    }
}

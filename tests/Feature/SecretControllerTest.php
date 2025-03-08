<?php

namespace Tests\Feature;

use App\Models\Secret;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class SecretControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_a_new_secret(): void
    {
        $response = $this->post(route('secrets.store'), [
            'content' => 'This is a secret',
            'expired_at' => now()->addDay(),
        ]);

        $response->assertRedirect(route('secrets.share', ['secret' => Secret::first()->uid]));
        $this->assertDatabaseHas('secrets', ['uid' => Secret::first()->uid]);
    }

    public function test_it_displays_a_secret(): void
    {
        $secret = Secret::create(['content' => Crypt::encrypt('This is a secret')]);

        $response = $this->get(route('secrets.show', $secret));

        $response->assertInertia(
            fn ($page) => $page
                ->component('Secret')
                ->where('secret', Crypt::decrypt($secret->content))
        );

        $this->assertDatabaseMissing('secrets', ['uid' => $secret->uid]);
    }

    public function test_it_destroys_a_secret(): void
    {
        $secret = Secret::create(['content' => 'This is a secret']);

        $response = $this->delete(route('secrets.destroy', $secret));

        $response->assertRedirect(route('home'));
        $this->assertDatabaseMissing('secrets', ['id' => $secret->id]);
    }
}

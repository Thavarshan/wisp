<?php

namespace Tests\Feature;

use App\Models\Secret;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShareSecretControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_the_share_secret_page()
    {
        $secret = Secret::factory()->create();

        $response = $this->get('/share?secret='.$secret->uid);

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page->component('Share')
                ->hasAll(['link', 'expired_at'])
        );
    }

    public function test_it_returns_404_when_secret_not_found()
    {
        $response = $this->get('/share?secret=nonexistent-uid');

        $response->assertStatus(404);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Secret;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShareSecretControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_the_share_secret_page(): void
    {
        $secret = Secret::create(['content' => 'This is a secret', 'expired_at' => now()->addDay()]);

        $response = $this->get(route('secrets.share', ['secret' => $secret->uid]));

        $response->assertInertia(
            fn ($page) => $page
                ->component('Share')
                ->where('link', $secret->getShareLink())
                ->where('expired_at', $secret->expired_at->format('F j, Y \a\t g:i:s A'))
        );
    }
}

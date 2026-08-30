<?php

namespace Tests\Feature;

use App\Enums\ExpirationOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function secret_creation_is_throttled_after_ten_requests(): void
    {
        RateLimiter::clear('ip:127.0.0.1');

        $payload = [
            'content' => 'rate limited content',
            'expiration' => ExpirationOption::ONE_HOUR->value,
            'password' => null,
        ];

        foreach (range(1, 10) as $attempt) {
            $this->postJson(route('secrets.store'), $payload)
                ->assertCreated();
        }

        $this->postJson(route('secrets.store'), $payload)
            ->assertTooManyRequests();
    }
}

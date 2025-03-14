<?php

namespace Tests\Unit;

use App\Models\Secret;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ClearExpiredSecretsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_clears_expired_secrets()
    {
        // Arrange: Create expired and non-expired secrets
        Secret::factory()->create(['expired_at' => now()->subDay()]);
        Secret::factory()->create(['expired_at' => now()->addDay()]);

        // Act: Run the command
        Artisan::call('secrets:clear-expired-secrets');

        // Assert: Only non-expired secrets should remain
        $this->assertDatabaseCount('secrets', 1);
        $this->assertDatabaseMissing('secrets', ['expired_at' => now()->subDay()]);
    }

    public function test_it_outputs_the_correct_message()
    {
        // Arrange: Create expired secrets
        Secret::factory()->count(3)->create(['expired_at' => now()->subDay()]);

        // Act: Run the command and capture the output
        $output = Artisan::call('secrets:clear-expired-secrets');

        // Assert: Check the output message
        $this->assertStringContainsString('3 expired secrets cleared.', Artisan::output());
    }
}

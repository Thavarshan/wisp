<?php

namespace Tests\Unit\Models;

use App\Models\Secret;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecretTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_secret()
    {
        $secret = Secret::factory()->create([
            'name' => 'Test Secret',
            'content' => 'Secret content',
        ]);

        $this->assertInstanceOf(Secret::class, $secret);
        $this->assertEquals('Test Secret', $secret->name);
        $this->assertNotNull($secret->uid);
        $this->assertNotEmpty($secret->content);
    }

    public function test_it_finds_secret_by_uid()
    {
        $secret = Secret::factory()->create();

        $found = Secret::findByUid($secret->uid);

        $this->assertInstanceOf(Secret::class, $found);
        $this->assertEquals($secret->id, $found->id);
    }

    public function test_find_by_uid_returns_null_when_not_found()
    {
        $found = Secret::findByUid('nonexistent-uid');

        $this->assertNull($found);
    }

    public function test_is_available_returns_true_when_secret_exists()
    {
        $secret = Secret::factory()->create();

        $result = Secret::isAvailable($secret->uid);

        $this->assertTrue($result);
    }

    public function test_is_available_returns_false_when_secret_does_not_exist()
    {
        $result = Secret::isAvailable('nonexistent-uid');

        $this->assertFalse($result);
    }

    public function test_has_expired_returns_false_for_non_expired_secret()
    {
        $secret = Secret::factory()->create([
            'expired_at' => Carbon::now()->addHour(),
        ]);

        $this->assertFalse($secret->hasExpired());
    }

    public function test_has_expired_returns_true_for_expired_secret()
    {
        $secret = Secret::factory()->create([
            'expired_at' => Carbon::now()->subHour(),
        ]);

        $this->assertTrue($secret->hasExpired());
    }

    public function test_has_expired_returns_false_when_expired_at_is_null()
    {
        // Since expired_at is required in the migration, we need to test this differently
        // Let's create a secret and then manually set expired_at to null
        $secret = Secret::factory()->make([
            'expired_at' => Carbon::now()->addHour(),
        ]);

        // Manually set expired_at to null to test the hasExpired method
        $secret->expired_at = null;

        $this->assertFalse($secret->hasExpired());
    }

    public function test_has_password_returns_true_when_password_is_set()
    {
        $secret = Secret::factory()->create([
            'password' => 'test-password',
        ]);

        $this->assertTrue($secret->hasPassword());
    }

    public function test_has_password_returns_false_when_password_is_null()
    {
        $secret = Secret::factory()->create([
            'password' => null,
        ]);

        $this->assertFalse($secret->hasPassword());
    }

    public function test_route_key_name_is_uid()
    {
        $secret = new Secret;

        $this->assertEquals('uid', $secret->getRouteKeyName());
    }

    public function test_it_has_fillable_attributes()
    {
        $fillable = (new Secret)->getFillable();

        $expected = ['uid', 'name', 'content', 'password', 'expired_at'];

        foreach ($expected as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    public function test_it_casts_expired_at_to_datetime()
    {
        $secret = Secret::factory()->create([
            'expired_at' => '2025-12-31 23:59:59',
        ]);

        $this->assertInstanceOf(Carbon::class, $secret->expired_at);
    }

    public function test_it_hides_sensitive_attributes()
    {
        $secret = Secret::factory()->create([
            'password' => 'secret-password',
        ]);

        $array = $secret->toArray();

        $this->assertArrayNotHasKey('password', $array);
        // Content is not hidden in this model
        $this->assertArrayHasKey('content', $array);
    }
}

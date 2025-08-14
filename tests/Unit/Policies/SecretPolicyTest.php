<?php

namespace Tests\Unit\Policies;

use App\Models\Secret;
use App\Models\User;
use App\Policies\SecretPolicy;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecretPolicyTest extends TestCase
{
    use RefreshDatabase;

    private SecretPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new SecretPolicy;
    }

    public function test_view_any_always_returns_false()
    {
        $user = User::factory()->create();

        $result = $this->policy->viewAny($user);

        $this->assertFalse($result);
    }

    public function test_view_returns_true_for_non_expired_secret()
    {
        $user = User::factory()->create();
        $secret = Secret::factory()->create([
            'expired_at' => Carbon::now()->addHour(),
        ]);

        $result = $this->policy->view($user, $secret);

        $this->assertTrue($result);
    }

    public function test_view_returns_true_for_non_expired_secret_with_null_user()
    {
        $secret = Secret::factory()->create([
            'expired_at' => Carbon::now()->addHour(),
        ]);

        $result = $this->policy->view(null, $secret);

        $this->assertTrue($result);
    }

    public function test_view_returns_false_for_expired_secret()
    {
        $user = User::factory()->create();
        $secret = Secret::factory()->create([
            'expired_at' => Carbon::now()->subHour(),
        ]);

        $result = $this->policy->view($user, $secret);

        $this->assertFalse($result);
    }

    public function test_create_always_returns_true()
    {
        $user = User::factory()->create();

        $result = $this->policy->create($user);

        $this->assertTrue($result);
    }

    public function test_create_returns_true_with_null_user()
    {
        $result = $this->policy->create(null);

        $this->assertTrue($result);
    }

    public function test_delete_always_returns_true()
    {
        $user = User::factory()->create();
        $secret = Secret::factory()->create();

        $result = $this->policy->delete($user, $secret);

        $this->assertTrue($result);
    }

    public function test_delete_returns_true_with_null_user()
    {
        $secret = Secret::factory()->create();

        $result = $this->policy->delete(null, $secret);

        $this->assertTrue($result);
    }
}

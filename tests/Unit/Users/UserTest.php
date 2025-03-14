<?php

namespace Tests\Unit\Users;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Users')]
class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_uniqnue_hash_id_db_trigger_is_enabled(): void
    {
        $user = User::factory()
            ->for(Organisation::factory()->create())
            ->create();

        $this->assertNotNull($user->uid);
    }
}

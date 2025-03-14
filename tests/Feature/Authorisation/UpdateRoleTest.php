<?php

namespace Tests\Feature\Authorisation;

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Authorisation')]
class UpdateRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_update(): void
    {
        $this->actor->assignRole(RoleEnum::ADMIN);

        $this->signIn($this->actor);

        $role = Role::factory()->create();
        $updatedData = ['name' => 'updated-role-name'];

        $response = $this->putJson(route('v1.roles.update', $role), $updatedData);

        $response->assertOk();
        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'updated-role-name']);
    }

    public function test_update_as_non_admin(): void
    {
        $this->actor->assignRole(
            Role::create(['name' => 'unknown'])
        );

        $this->signIn($this->actor);

        $role = Role::factory()->create();
        $updatedData = ['name' => 'updated-role-name'];

        $response = $this->putJson(route('v1.roles.update', $role), $updatedData);

        $response->assertForbidden();
    }
}

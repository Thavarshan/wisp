<?php

namespace Tests\Feature\Authorisation;

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Authorisation')]
class DeleteRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete(): void
    {
        $this->actor->assignRole(RoleEnum::ADMIN);

        $this->signIn($this->actor);

        $role = Role::factory()->create();

        $response = $this->deleteJson(route('v1.roles.destroy', $role));

        $response->assertOk();
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_delete_as_non_admin(): void
    {
        $this->actor->assignRole(Role::create([
            'name' => 'unknown',
            'organisation_id' => $this->actor->organisation_id,
        ]));

        $this->signIn($this->actor);

        $role = Role::factory()->create();

        $response = $this->deleteJson(route('v1.roles.destroy', $role));

        $response->assertForbidden();
    }
}

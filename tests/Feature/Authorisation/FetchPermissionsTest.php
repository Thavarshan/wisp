<?php

namespace Tests\Feature\Authorisation;

use App\Enums\Role as RoleEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Authorisation')]
class FetchPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        $this->actor->assignRole(
            $role = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value])
        );

        $this->signIn($this->actor);

        $permission = Permission::create([
            'name' => fake()->word,
        ]);

        $role->givePermissionTo($permission);

        $response = $this->getJson(route('v1.permissions.index'));

        $response->assertOk();
    }

    public function test_show(): void
    {
        $this->actor->assignRole(
            $role = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value])
        );

        $this->signIn($this->actor);

        $permission = Permission::create([
            'name' => fake()->word,
        ]);
        $role->givePermissionTo($permission);

        $response = $this->getJson(route('v1.permissions.show', $permission));

        $response->assertOk()
            ->assertJson([
                'name' => $permission->name,
            ]);
    }

    public function test_index_as_non_admin(): void
    {
        $this->actor->assignRole(Role::create([
            'name' => 'unknown',
            'organisation_id' => $this->actor->organisation_id,
        ]));

        $this->signIn($this->actor);

        $response = $this->getJson(route('v1.permissions.index'));

        $response->assertForbidden();
    }
}

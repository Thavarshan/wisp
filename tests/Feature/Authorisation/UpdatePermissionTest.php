<?php

namespace Tests\Feature\Authorisation;

use App\Enums\Role as RoleEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

#[Group('Authorisation')]
class UpdatePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_update(): void
    {
        $this->actor->assignRole(
            $role = Role::create(['name' => RoleEnum::ADMIN])
        );

        $this->signIn($this->actor);

        $permission = Permission::create([
            'name' => fake()->word,
        ]);
        $role->givePermissionTo($permission);

        $response = $this->putJson(route('v1.permissions.update', $permission), [
            'name' => 'updated-name',
        ]);

        $response->assertOk()
            ->assertJson([
                'name' => 'updated-name',
            ]);
    }

    public function test_update_with_missing_fields(): void
    {
        $this->actor->assignRole(
            $role = Role::create(['name' => RoleEnum::ADMIN])
        );

        $this->signIn($this->actor);

        $permission = Permission::create([
            'name' => fake()->word,
        ]);
        $role->givePermissionTo($permission);

        $response = $this->putJson(route('v1.permissions.update', $permission), [
            'name' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_update_as_non_admin(): void
    {
        $this->actor->assignRole(
            $role = Role::create(['name' => 'unknown'])
        );

        $this->signIn($this->actor);

        $permission = Permission::create([
            'name' => fake()->word,
        ]);
        $role->givePermissionTo($permission);

        $response = $this->putJson(route('v1.permissions.update', $permission), [
            'name' => 'updated-name',
        ]);

        $response->assertForbidden();
    }
}

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
class DeletePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_destroy(): void
    {
        $this->withoutExceptionHandling();
        $this->actor->assignRole(
            $role = Role::firstOrCreate([
                'name' => RoleEnum::ADMIN->value,
                'guard_name' => 'api',
            ])
        );

        $this->signIn($this->actor);

        $permission = Permission::create([
            'name' => fake()->word,
            'guard_name' => 'api',
        ]);
        $role->givePermissionTo($permission);

        $response = $this->deleteJson(route('v1.permissions.destroy', $permission));

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_destroy_as_non_admin(): void
    {
        $this->actor->assignRole(
            $role = Role::create([
                'name' => 'unknown',
                'guard_name' => 'api',
            ])
        );

        $this->signIn($this->actor);

        $permission = Permission::create([
            'name' => fake()->word,
            'guard_name' => 'api',
        ]);
        $role->givePermissionTo($permission);

        $response = $this->deleteJson(route('v1.permissions.destroy', $permission));

        $response->assertForbidden();
    }
}

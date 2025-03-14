<?php

namespace Tests\Feature\Authorisation;

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
        $this->actor->assignRole($role = Role::admin());

        $this->signIn($this->actor);

        $permission = Permission::create([
            'name' => fake()->word,
        ]);
        $role->givePermissionTo($permission);

        $response = $this->deleteJson(route('v1.permissions.destroy', $permission));

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_destroy_as_non_admin(): void
    {
        $this->actor->assignRole($role = Role::create([
            'name' => 'unknown',
            'organisation_id' => $this->actor->organisation_id,
        ]));

        $this->signIn($this->actor);

        $permission = Permission::create([
            'name' => fake()->word,
        ]);
        $role->givePermissionTo($permission);

        $response = $this->deleteJson(route('v1.permissions.destroy', $permission));

        $response->assertForbidden();
    }
}

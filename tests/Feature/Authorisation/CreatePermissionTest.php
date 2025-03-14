<?php

namespace Tests\Feature\Authorisation;

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

#[Group('Authorisation')]
class CreatePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_store(): void
    {
        $this->actor->assignRole(RoleEnum::ADMIN);

        $this->signIn($this->actor);

        $permission = Permission::make([
            'name' => 'permission-name',
            'guard_name' => 'api',
        ]);

        $response = $this->postJson(route('v1.permissions.store'), [
            'name' => $permission->name,
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'name' => $permission->name,
            ]);
    }

    public function test_store_with_missing_fields(): void
    {
        $this->actor->assignRole(RoleEnum::ADMIN);

        $this->signIn($this->actor);

        $response = $this->postJson(route('v1.permissions.store'), [
            'name' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_store_as_non_admin(): void
    {
        $this->actor->assignRole(
            Role::create(['name' => 'unknown'])
        );

        $this->signIn($this->actor);

        $permission = Permission::make([
            'name' => 'permission-name',
            'guard_name' => 'api',
        ]);

        $response = $this->postJson(route('v1.permissions.store'), [
            'name' => $permission->name,
        ]);

        $response->assertForbidden();
    }
}

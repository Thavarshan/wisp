<?php

namespace Tests\Feature\Authorisation;

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

#[Group('Authorisation')]
class CreateRoleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_store(): void
    {
        $this->actor->assignRole(RoleEnum::ADMIN);

        $this->signIn($this->actor);

        $role = Role::factory()->make();

        $response = $this->postJson(route('v1.roles.store'), [
            'name' => $role->name,
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'name' => $role->name,
            ]);
    }

    public function test_store_with_missing_fields(): void
    {
        $this->actor->assignRole(RoleEnum::ADMIN);

        $this->signIn($this->actor);

        $response = $this->postJson(route('v1.roles.store'), [
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

        $role = Role::factory()->make();

        $response = $this->postJson(route('v1.roles.store'), [
            'name' => $role->name,
        ]);

        $response->assertForbidden();
    }
}

<?php

namespace Tests\Feature\Users;

use App\Enums\Role;
use App\Models\Role as RoleModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

#[Group('Users')]
class CreateUsersTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_store_create(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $user = User::factory()->make();

        $response = $this->signIn($this->actor)
            ->postJson(route('v1.users.store'), [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'username' => $user->username,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    public function test_store_with_missing_fields(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $response = $this->postJson(route('v1.users.store'), [
            'first_name' => '',
            'last_name' => '',
            'username' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'username', 'email', 'password']);
    }

    public function test_store_with_invalid_email(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $user = User::factory()->make(['email' => 'invalid-email']);

        $response = $this->postJson(route('v1.users.store'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'username' => $user->username,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_store_with_non_matching_passwords(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $user = User::factory()->make();

        $response = $this->postJson(route('v1.users.store'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'username' => $user->username,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_store_as_non_admin(): void
    {
        $this->actor->assignRole(
            RoleModel::create([
                'name' => 'unknown',
                'organisation_id' => $this->actor->organisation_id,
            ])
        );

        $this->signIn($this->actor);

        $user = User::factory()->make();

        $response = $this->postJson(route('v1.users.store'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'username' => $user->username,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertForbidden();
    }
}

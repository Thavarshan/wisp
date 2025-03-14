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
class UpdateUsersTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_update(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $updatedData = User::factory()->make();

        $response = $this->putJson(route('v1.users.update', $this->actor->uid), [
            'first_name' => $updatedData->first_name,
            'last_name' => $updatedData->last_name,
            'username' => $updatedData->username,
            'email' => $updatedData->email,
        ]);

        $response->assertOk()
            ->assertJson([
                'first_name' => $updatedData->first_name,
                'last_name' => $updatedData->last_name,
                'username' => $updatedData->username,
                'email' => $updatedData->email,
            ]);
    }

    public function test_update_with_missing_fields(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $response = $this->putJson(route('v1.users.update', $this->actor->uid), [
            'first_name' => '',
            'last_name' => '',
            'username' => '',
            'email' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'username',
                'email',
            ]);
    }

    public function test_update_with_invalid_email(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $response = $this->putJson(route('v1.users.update', $this->actor->uid), [
            'first_name' => $this->actor->first_name,
            'last_name' => $this->actor->last_name,
            'username' => $this->actor->username,
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_update_as_non_admin(): void
    {
        $this->signIn($this->actor);

        $this->actor->assignRole(
            RoleModel::create([
                'name' => 'unknown',
                'organisation_id' => $this->actor->organisation_id,
            ])
        );

        $updatedData = User::factory()->make();

        $response = $this->putJson(route('v1.users.update', $this->actor->uid), [
            'first_name' => $updatedData->first_name,
            'last_name' => $updatedData->last_name,
            'username' => $updatedData->username,
            'email' => $updatedData->email,
        ]);

        $response->assertForbidden();
    }
}

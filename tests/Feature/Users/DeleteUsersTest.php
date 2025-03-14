<?php

namespace Tests\Feature\Users;

use App\Enums\Role;
use App\Models\Role as RoleModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

#[Group('Users')]
class DeleteUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete(): void
    {
        $this->withoutExceptionHandling();
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $userToDelete = User::factory()
            ->for($this->actor->organisation)
            ->create();

        $response = $this->deleteJson(route('v1.users.destroy', $userToDelete->uid));

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertNotNull($userToDelete->fresh()->deleted_at);
    }

    public function test_delete_as_non_admin(): void
    {
        $this->actor->assignRole(
            RoleModel::create([
                'name' => 'unknown',
                'organisation_id' => $this->actor->organisation_id,
            ])
        );

        $this->signIn($this->actor);

        $userToDelete = User::factory()
            ->for($this->actor->organisation)
            ->create();

        $response = $this->deleteJson(route('v1.users.destroy', $userToDelete->uid));

        $response->assertForbidden();
    }

    public function test_delete_non_existent_user(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $response = $this->deleteJson(route('v1.users.destroy', 'non-existent-uid'));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}

<?php

namespace Tests\Feature\Invitations;

use App\Enums\Role;
use App\Models\Invitation;
use App\Models\Role as RoleModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

#[Group('Invitations')]
class DeleteInvitationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_delete(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $invitation = Invitation::factory()
            ->for($this->actor->organisation)
            ->create();

        $response = $this->deleteJson(route('v1.invitations.destroy', $invitation));

        $response->assertStatus(Response::HTTP_NO_CONTENT);
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

        $invitation = Invitation::factory()
            ->for($this->actor->organisation)
            ->create();

        $response = $this->deleteJson(route('v1.invitations.destroy', $invitation));

        $response->assertForbidden();
    }
}

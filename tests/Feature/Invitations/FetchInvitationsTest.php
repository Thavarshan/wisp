<?php

namespace Tests\Feature\Invitations;

use App\Enums\Role;
use App\Models\Invitation;
use App\Models\Role as RoleModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Invitations')]
class FetchInvitationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetch_invitations_successfully(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        Invitation::factory(3)
            ->for($this->actor->organisation)
            ->create();

        $response = $this->getJson(route('v1.invitations.index'));

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    public function test_fetch_invitations_unauthorized(): void
    {
        $response = $this->getJson(route('v1.invitations.index'));

        $response->assertUnauthorized();
    }

    public function test_non_auhtorised_user_cannot_fetch_invitations(): void
    {
        $this->signIn($this->actor);

        $this->actor->assignRole(
            RoleModel::create([
                'name' => 'unknown',
                'organisation_id' => $this->actor->organisation_id,
            ])
        );

        $response = $this->getJson(route('v1.invitations.index'));

        $response->assertForbidden();
    }

    public function test_fetch_single_invitation_successfully(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $invitation = Invitation::factory()
            ->for($this->actor->organisation)
            ->create();

        $response = $this->getJson(route('v1.invitations.show', $invitation->uid));

        $response->assertOk();
        $response->assertJsonStructure([
            'uid',
            'email',
            'role',
            'created_at',
            'updated_at',
        ]);
    }

    public function test_fetch_single_invitation_unauthorized(): void
    {
        $invitation = Invitation::factory()->create();

        $response = $this->getJson(route('v1.invitations.show', $invitation->uid));

        $response->assertUnauthorized();
    }

    public function test_non_admin_cannot_fetch_single_invitation(): void
    {
        $this->signIn($this->actor);

        $this->actor->assignRole(
            RoleModel::create([
                'name' => 'unknown',
                'organisation_id' => $this->actor->organisation_id,
            ])
        );

        $invitation = Invitation::factory()
            ->for($this->actor->organisation)
            ->create();

        $response = $this->getJson(route('v1.invitations.show', $invitation->uid));

        $response->assertForbidden();
    }
}

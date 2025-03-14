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
class UpdateInvitationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_update(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $invitation = Invitation::factory()
            ->for($this->actor->organisation)
            ->create();

        $response = $this->putJson(route('v1.invitations.update', $invitation), [
            'email' => 'newemail@example.com',
            'role' => $invitation->role,
        ]);

        $response->assertOk()
            ->assertJson([
                'email' => 'newemail@example.com',
                'role' => $invitation->role,
            ]);
    }

    public function test_update_with_missing_fields(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $invitation = Invitation::factory()
            ->for($this->actor->organisation)
            ->create();

        $response = $this->putJson(route('v1.invitations.update', $invitation), [
            'email' => '',
            'role' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email', 'role']);
    }

    public function test_update_with_invalid_email(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $invitation = Invitation::factory()
            ->for($this->actor->organisation)
            ->create();

        $response = $this->putJson(route('v1.invitations.update', $invitation), [
            'email' => 'invalid-email',
            'role' => $invitation->role,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_update_as_non_admin(): void
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

        $response = $this->putJson(route('v1.invitations.update', $invitation), [
            'email' => 'newemail@example.com',
            'role' => $invitation->role,
        ]);

        $response->assertForbidden();
    }
}

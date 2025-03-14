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
class CreateInvitationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_store(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $invitation = Invitation::factory()->make();

        $response = $this->postJson(route('v1.invitations.store'), [
            'email' => $invitation->email,
            'role' => $invitation->role,
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'email' => $invitation->email,
                'role' => $invitation->role,
            ]);
    }

    public function test_store_with_missing_fields(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $response = $this->postJson(route('v1.invitations.store'), [
            'email' => '',
            'role' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email', 'role']);
    }

    public function test_store_with_invalid_email(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $invitation = Invitation::factory()->make(['email' => 'invalid-email']);

        $response = $this->postJson(route('v1.invitations.store'), [
            'email' => $invitation->email,
            'role' => $invitation->role,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);
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

        $invitation = Invitation::factory()->make();

        $response = $this->postJson(route('v1.invitations.store'), [
            'email' => $invitation->email,
            'role' => $invitation->role,
        ]);

        $response->assertForbidden();
    }
}

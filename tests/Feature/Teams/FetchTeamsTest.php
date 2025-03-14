<?php

namespace Tests\Feature\Teams;

use App\Enums\Role;
use App\Models\Role as RoleModel;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Teams')]
class FetchTeamsTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_super_admin_can_get_teams(): void
    {
        $this->actor->assignRole(Role::SUPER_ADMIN);

        $this->signIn($this->actor);

        $response = $this->getJson(route('v1.teams.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'uid',
                    'name',
                    'slug',
                    'description',
                    'user_id',
                    'organisation_id',
                    'owner',
                    'members',
                    'organisation',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_other_roles_can_get_teams(): void
    {
        $this->actor->assignRole(Role::STAFF);

        $this->signIn($this->actor);

        $response = $this->getJson(route('v1.teams.index'));

        $response->assertOk();
    }

    public function test_unauthorised_users_cannot_get_teams(): void
    {
        $this->actor->assignRole(
            RoleModel::create([
                'name' => 'unknown',
                'organisation_id' => $this->actor->organisation_id,
            ])
        );

        $this->signIn($this->actor);

        $response = $this->getJson(route('v1.teams.index'));

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_cannot_get_teams(): void
    {
        $response = $this->getJson(route('v1.teams.index'));

        $response->assertUnauthorized();
    }

    public function test_get_single_team(): void
    {
        $this->withoutExceptionHandling();
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $team = Team::factory()
            ->for($this->actor, 'owner')
            ->for($this->actor->organisation)
            ->create();

        $response = $this->getJson(route('v1.teams.show', $team->uid));

        $response->assertOk();
        $response->assertJsonStructure([
            'uid',
            'name',
            'slug',
            'description',
            'owner_id',
            'organisation_id',
            'owner',
            'members',
            'created_at',
            'updated_at',
        ]);
    }

    public function test_non_admin_cannot_get_single_team(): void
    {
        $this->actor->assignRole(
            RoleModel::create([
                'name' => 'unknown',
                'organisation_id' => $this->actor->organisation_id,
            ])
        );

        $this->signIn($this->actor);

        $team = Team::factory()
            ->for($this->actor, 'owner')
            ->for($this->actor->organisation)
            ->create();

        $response = $this->getJson(route('v1.teams.show', $team->uid));

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_cannot_get_single_team(): void
    {
        $team = Team::factory()
            ->for($this->actor, 'owner')
            ->for($this->actor->organisation)
            ->create();

        $response = $this->getJson(route('v1.teams.show', $team->uid));

        $response->assertUnauthorized();
    }
}

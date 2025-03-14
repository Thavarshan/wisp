<?php

namespace Tests\Feature\Teams;

use App\Enums\Role;
use App\Models\Role as RoleModel;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

#[Group('Teams')]
class UpdateTeamTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_update(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $team = Team::factory()
            ->for($this->actor, 'owner')
            ->for($this->actor->organisation)
            ->create();

        $response = $this->putJson(route('v1.teams.update', $team), [
            'name' => 'Updated Team Name',
            'description' => 'Updated Team Description',
        ]);

        $response->assertOk()
            ->assertJson([
                'name' => 'Updated Team Name',
                'description' => 'Updated Team Description',
            ]);
    }

    public function test_update_with_missing_fields(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $team = Team::factory()
            ->for($this->actor, 'owner')
            ->for($this->actor->organisation)
            ->create();

        $response = $this->putJson(route('v1.teams.update', $team), [
            'name' => '',
            'description' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_update_as_non_super_admin(): void
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

        $response = $this->putJson(route('v1.teams.update', $team), [
            'name' => 'Updated Team Name',
            'description' => 'Updated Team Description',
        ]);

        $response->assertForbidden();
    }
}

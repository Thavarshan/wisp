<?php

namespace Tests\Feature\Teams;

use App\Enums\Role;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

#[Group('Teams')]
class CreateTeamTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_store(): void
    {
        $this->actor->assignRole(Role::SUPER_ADMIN);

        $this->signIn($this->actor);

        $team = Team::factory()->make();

        $response = $this->postJson(route('v1.teams.store'), [
            'name' => $team->name,
            'description' => $team->description,
            'user_id' => $this->actor->id,
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'name' => $team->name,
                'description' => $team->description,
            ]);
    }

    public function test_store_with_missing_fields(): void
    {
        $this->actor->assignRole(Role::SUPER_ADMIN);

        $this->signIn($this->actor);

        $response = $this->postJson(route('v1.teams.store'), [
            'name' => '',
            'description' => '',
            'user_id' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name', 'user_id']);
    }

    public function test_store_as_non_super_admin(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $team = Team::factory()->make();

        $response = $this->postJson(route('v1.teams.store'), [
            'name' => $team->name,
            'description' => $team->description,
            'user_id' => $this->actor->id,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }
}

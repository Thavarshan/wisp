<?php

namespace Tests\Feature\Organisations;

use App\Enums\Role;
use App\Models\Organisation;
use App\Models\Role as RoleModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Organisations')]
class FetchOrganisationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_super_admin_can_get_organisations(): void
    {
        $this->actor->assignRole(Role::SUPER_ADMIN);

        $this->signIn($this->actor);

        $response = $this->getJson(route('v1.organisations.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'uid',
                    'name',
                    'slug',
                    'email',
                    'phone',
                    'website',
                    'logo',
                    'teams',
                    'users',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_no_other_roles_can_get_organisations(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $response = $this->getJson(route('v1.organisations.index'));

        $response->assertForbidden();
    }

    public function test_non_admin_cannot_get_organisations(): void
    {
        $this->actor->assignRole(
            RoleModel::create([
                'name' => 'unknown',
                'organisation_id' => $this->actor->organisation_id,
            ])
        );

        $this->signIn($this->actor);

        $response = $this->getJson(route('v1.organisations.index'));

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_cannot_get_organisations(): void
    {
        $response = $this->getJson(route('v1.organisations.index'));

        $response->assertUnauthorized();
    }

    public function test_get_single_organisation(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $organisation = Organisation::factory()->create();
        $organisation->associate($this->actor);

        $response = $this->getJson(route('v1.organisations.show', $organisation->uid));

        $response->assertOk();
        $response->assertJsonStructure([
            'uid',
            'name',
            'slug',
            'email',
            'phone',
            'website',
            'logo',
            'teams',
            'users',
            'created_at',
            'updated_at',
        ]);
    }

    public function test_non_admin_cannot_get_single_organisation(): void
    {
        $this->actor->assignRole(
            RoleModel::create([
                'name' => 'unknown',
                'organisation_id' => $this->actor->organisation_id,
            ])
        );

        $this->signIn($this->actor);

        $organisation = Organisation::factory()->create();

        $response = $this->getJson(route('v1.organisations.show', $organisation->uid));

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_cannot_get_single_organisation(): void
    {
        $organisation = Organisation::factory()->create();

        $response = $this->getJson(route('v1.organisations.show', $organisation->uid));

        $response->assertUnauthorized();
    }
}

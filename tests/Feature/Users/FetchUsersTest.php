<?php

namespace Tests\Feature\Users;

use App\Enums\Role;
use App\Models\Organisation;
use App\Models\Role as RoleModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Users')]
class FetchUsersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        User::factory(10)
            ->for(Organisation::factory()->create())
            ->create();

        $this->users = User::factory(10)
            ->for($this->organisation)
            ->create();
    }

    public function test_get_organisation_scoped_users(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $response = $this->getJson(route('v1.users.index'));

        $response->assertOk();
        $response->assertJsonCount(10, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'uid',
                    'first_name',
                    'last_name',
                    'username',
                    'email',
                    'phone',
                    'organisation',
                    'current_team_id',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

        $data = $response->json('data');

        foreach ($data as $user) {
            $this->assertEquals(
                $this->organisation->uid,
                $user['organisation']['uid']
            );
        }
    }

    public function test_non_admin_cannot_get_users(): void
    {
        $this->actor->assignRole(
            RoleModel::create([
                'name' => 'unknown',
                'organisation_id' => $this->actor->organisation_id,
            ])
        );

        $this->signIn($this->actor);

        $response = $this->getJson(route('v1.users.index'));

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_cannot_get_users(): void
    {
        $response = $this->getJson(route('v1.users.index'));

        $response->assertUnauthorized();
    }

    public function test_admin_can_get_all_users(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $response = $this->getJson(route('v1.users.index'));

        $response->assertOk();
        $response->assertJsonCount(10, 'data');
    }
}

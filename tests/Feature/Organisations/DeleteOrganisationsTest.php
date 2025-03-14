<?php

namespace Tests\Feature\Organisations;

use App\Enums\Role;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

#[Group('Organisations')]
class DeleteOrganisationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete(): void
    {
        $this->actor->assignRole(Role::SUPER_ADMIN);

        $this->signIn($this->actor);

        $organisation = Organisation::factory()->create();

        $response = $this->deleteJson(route('v1.organisations.destroy', $organisation));

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('organisations', ['id' => $organisation->id]);
    }

    public function test_delete_as_non_super_admin(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $organisation = Organisation::factory()->create();

        $response = $this->deleteJson(route('v1.organisations.destroy', $organisation));

        $response->assertForbidden();
    }
}

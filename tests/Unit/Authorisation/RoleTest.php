<?php

namespace Tests\Unit\Authorisation;

use App\Models\Organisation;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Authorisation')]
class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_are_scoped_by_organisation(): void
    {
        $initialOrganisation = Organisation::factory()->create();
        $initialRole = Role::make(['name' => 'Test-Role']);
        $initialRole->organisation()->associate($initialOrganisation);
        $initialRole->save();

        $this->assertDatabaseHas('roles', [
            'name' => 'Test-Role',
            'organisation_id' => $initialOrganisation->id,
        ]);

        $this->signIn($this->actor);

        $newRole = Role::make(['name' => 'Test-Role']);
        $newRole->organisation()->associate($this->actor->organisation);
        $newRole->save();

        $this->assertDatabaseHas('roles', [
            'name' => 'Test-Role',
            'organisation_id' => $this->actor->organisation->id,
        ]);

        $fetchedRole = Role::firstOrCreate(['name' => 'Test-Role']);

        $this->assertNotEquals($initialRole->id, $fetchedRole->id);
        $this->assertEquals($newRole->id, $fetchedRole->id);
        $this->assertNotEquals($initialOrganisation->id, $this->actor->organisation->id);
        $this->assertEquals($this->actor->organisation->id, $fetchedRole->organisation_id);
    }
}

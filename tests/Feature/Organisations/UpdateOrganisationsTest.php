<?php

namespace Tests\Feature\Organisations;

use App\Enums\Role;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

#[Group('Organisations')]
class UpdateOrganisationsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_update(): void
    {
        $this->actor->assignRole(Role::SUPER_ADMIN);

        $this->signIn($this->actor);

        $organisation = Organisation::factory()->create();
        $updatedData = Organisation::factory()->make();

        $response = $this->putJson(route('v1.organisations.update', $organisation), [
            'name' => $updatedData->name,
            'slug' => $updatedData->slug,
            'email' => $updatedData->email,
            'phone' => $updatedData->phone,
            'website' => $updatedData->website,
            'logo' => $updatedData->logo,
        ]);

        $response->assertOk()
            ->assertJson([
                'name' => $updatedData->name,
                'email' => $updatedData->email,
            ]);
    }

    public function test_update_with_missing_fields(): void
    {
        $this->actor->assignRole(Role::SUPER_ADMIN);

        $this->signIn($this->actor);

        $organisation = Organisation::factory()->create();

        $response = $this->putJson(route('v1.organisations.update', $organisation), [
            'name' => '',
            'slug' => '',
            'email' => '',
            'phone' => '',
            'website' => '',
            'logo' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name', 'phone', 'email']);
    }

    public function test_update_with_invalid_email(): void
    {
        $this->actor->assignRole(Role::SUPER_ADMIN);

        $this->signIn($this->actor);

        $organisation = Organisation::factory()->create();
        $updatedData = Organisation::factory()->make(['email' => 'invalid-email']);

        $response = $this->putJson(route('v1.organisations.update', $organisation), [
            'name' => $updatedData->name,
            'slug' => $updatedData->slug,
            'email' => $updatedData->email,
            'phone' => $updatedData->phone,
            'website' => $updatedData->website,
            'logo' => $updatedData->logo,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_update_with_invalid_phone(): void
    {
        $this->actor->assignRole(Role::SUPER_ADMIN);

        $this->signIn($this->actor);

        $organisation = Organisation::factory()->create();
        $updatedData = Organisation::factory()->make(['phone' => 'invalid-phone']);

        $response = $this->putJson(route('v1.organisations.update', $organisation), [
            'name' => $updatedData->name,
            'slug' => $updatedData->slug,
            'email' => $updatedData->email,
            'phone' => $updatedData->phone,
            'website' => $updatedData->website,
            'logo' => $updatedData->logo,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['phone']);
    }

    public function test_update_as_non_super_admin(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $organisation = Organisation::factory()->create();
        $updatedData = Organisation::factory()->make();

        $response = $this->putJson(route('v1.organisations.update', $organisation), [
            'name' => $updatedData->name,
            'slug' => $updatedData->slug,
            'email' => $updatedData->email,
            'phone' => $updatedData->phone,
            'website' => $updatedData->website,
            'logo' => $updatedData->logo,
        ]);

        $response->assertForbidden();
    }
}

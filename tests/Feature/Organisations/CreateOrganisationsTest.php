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
class CreateOrganisationsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_store(): void
    {
        $this->actor->assignRole(Role::SUPER_ADMIN);

        $this->signIn($this->actor);

        $organisation = Organisation::factory()->make();

        $response = $this->postJson(route('v1.organisations.store'), [
            'name' => $organisation->name,
            'slug' => $organisation->slug,
            'email' => $organisation->email,
            'phone' => $organisation->phone,
            'website' => $organisation->website,
            'logo' => $organisation->logo,
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'name' => $organisation->name,
                'email' => $organisation->email,
            ]);
    }

    public function test_store_with_missing_fields(): void
    {
        $this->actor->assignRole(Role::SUPER_ADMIN);

        $this->signIn($this->actor);

        $response = $this->postJson(route('v1.organisations.store'), [
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

    public function test_store_with_invalid_email(): void
    {
        $this->actor->assignRole(Role::SUPER_ADMIN);

        $this->signIn($this->actor);

        $organisation = Organisation::factory()->make(['email' => 'invalid-email']);

        $response = $this->postJson(route('v1.organisations.store'), [
            'name' => $organisation->name,
            'slug' => $organisation->slug,
            'email' => $organisation->email,
            'phone' => $organisation->phone,
            'website' => $organisation->website,
            'logo' => $organisation->logo,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_store_as_non_super_admin(): void
    {
        $this->actor->assignRole(Role::ADMIN);

        $this->signIn($this->actor);

        $organisation = Organisation::factory()->make();

        $response = $this->postJson(route('v1.organisations.store'), [
            'name' => $organisation->name,
            'slug' => $organisation->slug,
            'email' => $organisation->email,
            'phone' => $organisation->phone,
            'website' => $organisation->website,
            'logo' => $organisation->logo,
        ]);

        $response->assertForbidden();
    }
}

<?php

namespace Tests\Feature\Onboarding;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Onboarding')]
class OnboardNewOrganisationTest extends TestCase
{
    use RefreshDatabase;

    public function test_onboard_new_organisation(): void
    {
        $this->signInAsClient();

        $data = [
            'organisation' => [
                'name' => 'Test Organisation',
                'email' => 'org@example.com',
                'phone' => '1234567890',
            ],
            'owner' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'username' => 'johndoe',
                'email' => 'john@example.com',
                'phone' => '0987654321',
                'password' => 'password',
                'password_confirmation' => 'password',
            ],
        ];

        $response = $this->postJson(route('v1.onboarding'), $data);

        $response->assertCreated()
            ->assertJson(['message' => 'Organisation created.']);

        $this->assertDatabaseHas('organisations', [
            'name' => 'Test Organisation',
            'email' => 'org@example.com',
            'phone' => '1234567890',
        ]);
    }
}

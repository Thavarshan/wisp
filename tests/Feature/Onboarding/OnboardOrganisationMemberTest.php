<?php

namespace Tests\Feature\Onboarding;

use App\Models\Invitation;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Onboarding')]
class OnboardOrganisationMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_onboard_organisation_member(): void
    {
        $email = 'user@test.org';
        $organisation = Organisation::factory()->create();
        $organisation->associate($this->actor);
        Invitation::factory()
            ->for($this->organisation)
            ->create(['email' => $email]);

        $this->postJson(route('v1.register'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1234567890',
            'username' => 'johndoe',
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertCreated();
    }

    public function test_only_members_with_invition_can_be_onboarded(): void
    {
        $this->postJson(route('v1.register'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1234567890',
            'username' => 'johndoe',
            'email' => 'foreign@email.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertForbidden();
    }
}

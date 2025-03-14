<?php

namespace Tests\Feature\Authentication;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

#[Group('Authentication')]
class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_login(): void
    {
        $this->withoutExceptionHandling();
        $organisation = Organisation::factory()->create();
        $user = User::factory()
            ->for($organisation)
            ->create([
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);

        $response = $this->postJson(route('v1.login'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_ACCEPTED)
            ->assertJsonStructure([
                'token',
                'user' => [
                    'uid',
                    'current_team_id',
                    'organisation_id',
                    'first_name',
                    'last_name',
                    'name',
                    'email',
                    'username',
                    'phone',
                    'about',
                    'date_of_birth',
                    'email_verified_at',
                    'meta',
                    'teams',
                    'organisation',
                    'roles',
                    'current_team',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_failed_login_with_invalid_credentials(): void
    {
        $organisation = Organisation::factory()->create();
        $user = User::factory()
            ->for($organisation)
            ->create([
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);

        $response = $this->postJson(route('v1.login'), [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }

    public function test_failed_login_with_nonexistent_user(): void
    {
        $response = $this->postJson(route('v1.login'), [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(401);
    }
}

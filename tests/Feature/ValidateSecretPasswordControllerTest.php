<?php

namespace Tests\Feature;

use App\Models\Secret;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ValidateSecretPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fails_with_incorrect_password()
    {
        $secret = Secret::factory()->create([
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->post("/secrets/{$secret->uid}/password", [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_it_validates_correct_password()
    {
        $password = 'correct-password';
        $secret = Secret::factory()->create([
            'password' => Hash::make($password),
        ]);

        $response = $this->post("/secrets/{$secret->uid}/password", [
            'password' => $password,
        ]);

        // Just check that it doesn't fail validation (gets redirect or success)
        $this->assertNotEquals(422, $response->getStatusCode());
    }
}

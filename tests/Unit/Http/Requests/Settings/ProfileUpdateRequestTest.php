<?php

namespace Tests\Unit\Http\Requests\Settings;

use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileUpdateRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_correct_validation_rules()
    {
        $user = User::factory()->create();

        $request = new ProfileUpdateRequest;
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('email', $rules);

        $this->assertContains('required', $rules['name']);
        $this->assertContains('string', $rules['name']);
        $this->assertContains('max:255', $rules['name']);

        $this->assertContains('required', $rules['email']);
        $this->assertContains('string', $rules['email']);
        $this->assertContains('lowercase', $rules['email']);
        $this->assertContains('email', $rules['email']);
        $this->assertContains('max:255', $rules['email']);
    }

    public function test_email_must_be_unique_except_for_current_user()
    {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);

        $request = new ProfileUpdateRequest;
        $request->setUserResolver(function () use ($user1) {
            return $user1;
        });

        $rules = $request->rules();

        // The email rule should include a unique rule that ignores the current user's ID
        $emailRules = $rules['email'];
        $uniqueRule = null;

        foreach ($emailRules as $rule) {
            if (is_object($rule) && method_exists($rule, 'ignore')) {
                $uniqueRule = $rule;
                break;
            }
        }

        $this->assertNotNull($uniqueRule, 'Unique rule should exist');
    }
}

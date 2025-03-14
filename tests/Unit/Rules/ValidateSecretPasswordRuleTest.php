<?php

namespace Tests\Unit\Rules;

use App\Models\Secret;
use App\Rules\ValidateSecretPasswordRule;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ValidateSecretPasswordRuleTest extends TestCase
{
    public function test_validate_passes_when_no_password()
    {
        $secret = $this->createMock(Secret::class);
        $secret->method('hasPassword')->willReturn(false);

        $rule = new ValidateSecretPasswordRule($secret);

        $fail = function ($message) {
            $this->fail('Validation should not fail when there is no password.');
        };

        $rule->validate('password', 'any-value', $fail);

        // Add assertion to ensure the test performs an assertion
        $this->assertTrue(true);
    }

    public function test_validate_fails_when_password_is_incorrect()
    {
        $secret = $this->createMock(Secret::class);
        $secret->method('hasPassword')->willReturn(true);
        $secret->password = Hash::make('correct-password');

        $rule = new ValidateSecretPasswordRule($secret);

        $fail = function ($message) {
            $this->assertEquals('The provided password is incorrect.', $message);
        };

        $rule->validate('password', 'wrong-password', $fail);
    }

    public function test_validate_passes_when_password_is_correct()
    {
        $secret = new Secret;

        $hashedPassword = Hash::make('correct-password');
        $secret->password = $hashedPassword;

        $rule = new ValidateSecretPasswordRule($secret);

        $fail = function ($message) {
            $this->fail('Validation should not fail when the password is correct.');
        };

        $rule->validate('password', 'correct-password', $fail);

        $this->assertTrue(true, 'Validation should pass when the password is correct.');
    }
}

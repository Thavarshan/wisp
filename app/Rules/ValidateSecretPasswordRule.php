<?php

namespace App\Rules;

use App\Models\Secret;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

class ValidateSecretPasswordRule implements ValidationRule
{
    /**
     * Create a new rule instance.
     */
    public function __construct(protected Secret $secret) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->secret->hasPassword()) {
            return;
        }

        if (! Hash::check($value, $this->secret->password)) {
            $fail(__('The provided password is incorrect.'));
        }
    }
}

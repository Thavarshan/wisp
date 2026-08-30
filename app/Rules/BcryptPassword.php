<?php

namespace App\Rules;

use App\Models\Secret;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BcryptPassword implements ValidationRule
{
    /**
     * Validate a password against bcrypt's byte-length and input constraints.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        if (str_contains($value, "\0")) {
            $fail('The :attribute may not contain null characters.');

            return;
        }

        if (strlen($value) > Secret::MAX_PASSWORD_BYTES) {
            $fail('The :attribute may not exceed '.Secret::MAX_PASSWORD_BYTES.' UTF-8 bytes.');
        }
    }
}

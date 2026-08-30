<?php

namespace App\Http\Requests;

use App\Models\Secret;
use Illuminate\Foundation\Http\FormRequest;

class RevealSecretRequest extends FormRequest
{
    /**
     * Determine whether the recipient may reveal a secret.
     *
     * @return bool Always true; the secret token controls access.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules for a reveal request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'password' => [
                'nullable',
                'string',
                'max:'.Secret::MAX_PASSWORD_LENGTH,
            ],
        ];
    }

    /**
     * Get the custom messages for the validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'password.string' => 'Enter a valid password.',
            'password.max' => 'The password may not exceed :max characters.',
        ];
    }
}

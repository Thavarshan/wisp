<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RevokeSecretRequest extends FormRequest
{
    /**
     * Determine whether the requester may revoke a secret.
     *
     * @return bool Always true; the revocation token controls access.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules for a revocation request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'revocation_token' => [
                'required',
                'string',
                'size:64',
                'regex:/\A[0-9a-f]{64}\z/',
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
            'revocation_token.required' => 'Enter the revocation token.',
            'revocation_token.size' => 'The revocation token is invalid.',
            'revocation_token.regex' => 'The revocation token is invalid.',
        ];
    }
}

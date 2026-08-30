<?php

namespace App\Http\Requests;

use App\Enums\ExpirationOption;
use App\Models\Secret;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSecretRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => [
                'required',
                'string',
                'max:'.Secret::MAX_CONTENT_LENGTH,
            ],
            'expiration' => [
                'required',
                'string',
                Rule::enum(ExpirationOption::class),
            ],
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
            'content.required' => 'Add some content before creating a secret.',
            'content.max' => 'Secret content may not exceed :max characters.',
            'expiration.required' => 'Choose when this secret should expire.',
            'expiration.enum' => 'Choose a valid expiration option.',
            'password.max' => 'The password may not exceed :max characters.',
        ];
    }
}

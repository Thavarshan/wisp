<?php

namespace App\Http\Requests;

use App\Enums\ExpirationOption;
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
            'content' => ['required', 'string', 'max:10000'], // 10KB limit for secret content
            'expiration' => ['required', 'string', Rule::enum(ExpirationOption::class)],
            'password' => ['nullable', 'string', 'max:255'],
        ];
    }
}

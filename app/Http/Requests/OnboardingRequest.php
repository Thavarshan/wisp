<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OnboardingRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'organisation.name' => ['required', 'string', 'max:255'],
            'organisation.email' => ['required', 'string', 'email', 'unique:organisations,email'],
            'organisation.phone' => ['required', 'string', 'unique:organisations,phone'],
            'owner.first_name' => ['required', 'string', 'max:255'],
            'owner.last_name' => ['required', 'string', 'max:255'],
            'owner.username' => ['required', 'string', 'max:255'],
            'owner.email' => ['required', 'string', 'email', 'unique:users,email'],
            'owner.phone' => ['required', 'string', 'unique:users,phone'],
            'owner.password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}

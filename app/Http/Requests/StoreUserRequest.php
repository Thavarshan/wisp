<?php

namespace App\Http\Requests;

use App\Enums\Permission;
use App\Models\User;
use App\Rules\PhoneNumberRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows(Permission::CREATE->value, User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:1', 'max:255'],
            'last_name' => ['required', 'string', 'min:1', 'max:255'],
            'email' => ['required', 'string', 'email', 'min:1', 'max:255'],
            'username' => ['required', 'string', 'min:1', 'max:255'],
            'phone' => ['nullable', 'string', 'min:1', 'max:255', new PhoneNumberRule],
            'about' => ['nullable', 'string', 'min:1', 'max:1024'],
            'date_of_birth' => ['nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07'],
            'password' => ['required', 'string', 'min:1', 'max:255', 'confirmed'],
        ];
    }
}

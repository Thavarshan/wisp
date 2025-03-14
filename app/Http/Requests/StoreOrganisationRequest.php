<?php

namespace App\Http\Requests;

use App\Enums\Permission;
use App\Models\Organisation;
use App\Rules\PhoneNumberRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreOrganisationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows(Permission::CREATE->value, Organisation::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'unique:organisations'],
            'phone' => ['required', 'string', 'max:255', new PhoneNumberRule],
            'website' => ['sometimes', 'url:http,https', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:255'],
        ];
    }
}

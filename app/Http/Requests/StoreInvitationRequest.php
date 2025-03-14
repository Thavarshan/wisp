<?php

namespace App\Http\Requests;

use App\Enums\Permission;
use App\Models\Invitation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows(Permission::CREATE->value, Invitation::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:invitations,email'],
            'role' => ['required', 'string', 'exists:roles,name'],
        ];
    }
}

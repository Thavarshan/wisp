<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RevokeSecretRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['revocation_token' => ['required', 'string', 'size:64', 'regex:/\A[0-9a-f]{64}\z/']];
    }
}

<?php

namespace App\Http\Requests;

use App\Enums\ExpirationOption;
use App\Models\Secret;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreSecretRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', Secret::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:10000'], // 10KB limit for secret content
            'expired_at' => ['required', 'string', Rule::enum(ExpirationOption::class)],
            'password' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Handle a passed validation attempt.
     */
    public function getValidatedData(): array
    {
        $data = $this->validated();

        return array_merge($data, [
            'expired_at' => ExpirationOption::parse($data['expired_at']),
        ]);
    }
}

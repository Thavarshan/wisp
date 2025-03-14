<?php

namespace App\Http\Requests;

use App\Models\Invitation;

class RegisterUserRequest extends StoreUserRequest
{
    /**
     * The invitation instance.
     */
    protected ?Invitation $invitation = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $invitation = Invitation::where('email', $this->email)->first();

        if (is_null($invitation)) {
            return false;
        }

        $this->invitation = $invitation;

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function invitation(): ?Invitation
    {
        return $this->invitation;
    }
}

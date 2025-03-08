<?php

namespace App\Http\Controllers;

use App\Http\Requests\SecretPasswordRequest;
use App\Models\Secret;
use Illuminate\Http\RedirectResponse;

class ValidateSecretPasswordController extends Controller
{
    /**
     * Validate the secret password.
     */
    public function __invoke(
        SecretPasswordRequest $request,
        Secret $secret
    ): RedirectResponse {
        $request->validatePassword($secret->password);

        $secret->password = null;
        $secret->save();

        return redirect()->back();
    }
}

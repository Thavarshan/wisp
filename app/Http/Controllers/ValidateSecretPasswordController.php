<?php

namespace App\Http\Controllers;

use App\Http\Requests\SecretPasswordRequest;
use App\Models\Secret;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ValidateSecretPasswordController extends Controller
{
    /**
     * Validate the secret password.
     */
    public function __invoke(
        SecretPasswordRequest $request,
        Secret $secret
    ): RedirectResponse|Response {
        // Use database transaction to prevent race conditions
        DB::transaction(function () use ($secret) {
            $secret->password = null;
            $secret->save();
        });

        if ($request->wantsJson()) {
            return response()->noContent();
        }

        return redirect()->back();
    }
}

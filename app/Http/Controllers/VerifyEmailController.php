<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyEmailRequest;
use Illuminate\Auth\Events\Verified;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(VerifyEmailRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->noContent();
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return response()->noContent();
    }
}

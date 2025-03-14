<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->noContent();
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Email verification link sent on your email'], Response::HTTP_ACCEPTED);
    }
}

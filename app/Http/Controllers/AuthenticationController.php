<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        abort_unless(
            Auth::attempt($request->only('email', 'password')),
            Response::HTTP_UNAUTHORIZED
        );

        $user = Auth::user();
        $token = $user
            ->createToken(now()->toDateTimeString())
            ->accessToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ], Response::HTTP_ACCEPTED);
    }
}

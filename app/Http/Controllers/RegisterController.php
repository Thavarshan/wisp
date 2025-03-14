<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function __invoke(RegisterUserRequest $request): Response
    {
        $user = User::createFromInvitation(
            $request->invitation(),
            $request->validated()
        );

        // event(new Registered($user));

        return response()->json(
            new UserResource($user),
            Response::HTTP_CREATED
        );
    }
}

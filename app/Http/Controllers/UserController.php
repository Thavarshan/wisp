<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Events\UserDeletionRequested;
use App\Filters\UserFilter;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(UserFilter $filter): JsonResource
    {
        Gate::authorize(Permission::VIEW_ANY->value, User::class);

        $users = User::filter($filter)
            ->with(['organisation'])
            ->latest()
            ->paginate($request->per_page ?? 10);

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): Response
    {
        $user = User::create($request->validated());

        return response()->json(
            new UserResource($user),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): Response
    {
        Gate::authorize(Permission::VIEW->value, $user);

        $user->load(['roles', 'teams', 'organisation', 'currentTeam']);

        return response()->json(new UserResource($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): Response
    {
        Gate::authorize(Permission::UPDATE->value, $user);

        $user->update($request->validated());

        return response()->json(new UserResource($user));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): Response
    {
        Gate::authorize(Permission::DELETE->value, $user);

        UserDeletionRequested::dispatch($user);

        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Filters\RoleFilter;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(RoleFilter $filter): JsonResource
    {
        Gate::authorize(Permission::VIEW_ANY->value, Role::class);

        $roles = Role::filter($filter)->get();

        return RoleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): Response
    {
        $role = Role::create($request->validated());

        return response()->json(
            new RoleResource($role),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): Response
    {
        Gate::authorize(Permission::VIEW->value, $role);

        $role->load('permissions');

        return response()->json(new RoleResource($role));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): Response
    {
        $role->update($request->validated());

        return response()->json(new RoleResource($role));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): Response
    {
        Gate::authorize(Permission::DELETE->value, $role);

        $role->delete();

        return response()->json(new RoleResource($role));
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\Permission as PermissionEnum;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        Gate::authorize(PermissionEnum::VIEW_ANY->value, Permission::class);

        $permissions = Permission::all();

        return PermissionResource::collection($permissions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request): Response
    {
        $permission = Permission::create($request->all());

        return response()->json(
            new PermissionResource($permission),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission): Response
    {
        Gate::authorize(PermissionEnum::VIEW->value, $permission);

        $permission->load('permissions');

        return response()->json(new PermissionResource($permission));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdatePermissionRequest $request,
        Permission $permission
    ): Response {
        $permission->update($request->validated());

        return response()->json(new PermissionResource($permission));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission): Response
    {
        Gate::authorize(PermissionEnum::DELETE->value, $permission);

        $permission->delete();

        return response()->noContent();
    }
}

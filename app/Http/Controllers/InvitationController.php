<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Filters\InvitationFilter;
use App\Http\Requests\StoreInvitationRequest;
use App\Http\Requests\UpdateInvitationRequest;
use App\Http\Resources\InvitationResource;
use App\Models\Invitation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class InvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(InvitationFilter $filter): JsonResource
    {
        Gate::authorize(Permission::VIEW_ANY->value, Invitation::class);

        $invitations = Invitation::filter($filter)->get();

        return InvitationResource::collection($invitations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvitationRequest $request): Response
    {
        $invitation = Invitation::create($request->validated());

        return response()->json(
            new InvitationResource($invitation),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Invitation $invitation): Response
    {
        Gate::authorize(Permission::VIEW->value, $invitation);

        $invitation->load(['organisation']);

        return response()->json(new InvitationResource($invitation));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateInvitationRequest $request,
        Invitation $invitation
    ): Response {
        Gate::authorize(Permission::UPDATE->value, $invitation);

        $invitation->update($request->validated());

        return response()->json(new InvitationResource($invitation));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invitation $invitation): Response
    {
        Gate::authorize(Permission::DELETE->value, $invitation);

        $invitation->delete();

        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Events\OrganisationDeletionRequested;
use App\Filters\OrganisationFilter;
use App\Http\Requests\StoreOrganisationRequest;
use App\Http\Requests\UpdateOrganisationRequest;
use App\Http\Resources\OrganisationResource;
use App\Models\Organisation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class OrganisationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(OrganisationFilter $filter): JsonResource
    {
        Gate::authorize(Permission::VIEW_ANY->value, Organisation::class);

        $organisations = Organisation::filter($filter)
            ->latest()
            ->paginate($request->per_page ?? 10);

        return OrganisationResource::collection($organisations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganisationRequest $request): Response
    {
        $organisation = Organisation::create($request->validated());

        return response()->json(
            new OrganisationResource($organisation),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Organisation $organisation): Response
    {
        Gate::authorize(Permission::VIEW->value, $organisation);

        $organisation->load(['users', 'teams']);

        return response()->json(new OrganisationResource($organisation));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateOrganisationRequest $request,
        Organisation $organisation
    ): Response {
        Gate::authorize(Permission::UPDATE->value, $organisation);

        $organisation->update($request->validated());

        return response()->json(new OrganisationResource($organisation));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organisation $organisation): Response
    {
        Gate::authorize(Permission::DELETE->value, $organisation);

        OrganisationDeletionRequested::dispatch($organisation);

        return response()->noContent();
    }
}

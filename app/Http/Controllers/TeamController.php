<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Events\TeamDeletionRequested;
use App\Filters\TeamFilter;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(TeamFilter $filter, Request $request): JsonResource
    {
        Gate::authorize(Permission::VIEW_ANY->value, Team::class);

        $teams = Team::filter($filter)
            ->latest()
            ->paginate($request->per_page ?? 10);

        return TeamResource::collection($teams);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeamRequest $request): Response
    {
        $team = Team::create($request->validated());

        return response()->json(
            new TeamResource($team),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team): Response
    {
        Gate::authorize(Permission::VIEW->value, $team);

        $team->load(['members', 'owner']);

        return response()->json(new TeamResource($team));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeamRequest $request, Team $team): Response
    {
        Gate::authorize(Permission::UPDATE->value, $team);

        $team->update($request->validated());

        return response()->json(new TeamResource($team));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team): Response
    {
        Gate::authorize(Permission::DELETE->value, $team);

        // TeamDeletionRequested::dispatch($team);

        return response()->noContent();
    }
}

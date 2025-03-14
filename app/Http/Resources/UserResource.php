<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uid' => $this->uid,
            'current_team_id' => $this->current_team_id,
            'organisation_id' => $this->organisation_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'phone' => $this->phone,
            'about' => $this->about,
            'date_of_birth' => $this->date_of_birth,
            'email_verified_at' => $this->email_verified_at,
            'meta' => $this->meta,
            'teams' => TeamResource::collection($this->whenLoaded('teams')),
            'organisation' => new OrganisationResource($this->whenLoaded('organisation')),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'current_team' => new TeamResource($this->whenLoaded('currentTeam')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

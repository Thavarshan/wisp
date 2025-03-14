<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'owner_id' => $this->user_id,
            'organisation_id' => $this->organisation_id,
            'owner' => new UserResource($this->whenLoaded('owner')),
            'members' => UserResource::collection($this->whenLoaded('members')),
            'organisation' => new OrganisationResource($this->whenLoaded('organisation')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

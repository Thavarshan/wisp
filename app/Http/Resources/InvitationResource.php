<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvitationResource extends JsonResource
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
            'email' => $this->email,
            'role' => $this->role,
            'organisation' => $this->whenLoaded('organisation', fn () => $this->organisation->only('uid', 'name')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

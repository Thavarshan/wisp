<?php

namespace App\Events;

use App\Models\Organisation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrganisationDeletionRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(protected Organisation $organisation) {}

    /**
     * Get the organisation.
     */
    public function getOrganisation(): Organisation
    {
        return $this->organisation;
    }
}

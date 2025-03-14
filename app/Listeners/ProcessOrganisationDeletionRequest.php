<?php

namespace App\Listeners;

use App\Events\OrganisationDeletionRequested;
use Illuminate\Support\Facades\DB;

class ProcessOrganisationDeletionRequest
{
    /**
     * Handle the event.
     */
    public function handle(OrganisationDeletionRequested $event): void
    {
        dispatch(fn () => DB::transaction(function () use ($event) {
            tap($event->getOrganisation(), function ($organisation) {
                $organisation->teams()->delete();
                $organisation->users()->delete();
                $organisation->delete();
            });
        }));
    }
}

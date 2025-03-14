<?php

namespace App\Listeners;

use App\Events\UserDeletionRequested;
use App\Jobs\DeleteUser;

class ProcessUserDeletionRequest
{
    /**
     * Handle the event.
     */
    public function handle(UserDeletionRequested $event): void
    {
        DeleteUser::dispatch($event->getUser());
    }
}

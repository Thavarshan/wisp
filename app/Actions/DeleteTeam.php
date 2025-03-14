<?php

namespace App\Actions;

use App\Models\Team;

class DeleteTeam
{
    /**
     * Delete the given team.
     */
    public function delete(Team $team): void
    {
        $team->purge();
    }
}

<?php

namespace App\Jobs;

use App\Actions\DeleteTeam;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class DeleteUser implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new action instance.
     */
    public function __construct(protected User $user) {}

    /**
     * Execute the job.
     */
    public function handle(DeleteTeam $deletesTeams): void
    {
        DB::transaction(function () use ($deletesTeams) {
            $this->user->teams()->detach();

            $this->user->ownedTeams->each(
                fn () => $deletesTeams->delete($team)
            );

            $this->user->tokens->each->delete();
            $this->user->delete();
        });
    }
}

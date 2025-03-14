<?php

namespace App\Models\Traits;

use App\Models\Membership;
use App\Models\Team;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

trait HasTeams
{
    /**
     * Create a personal team for the user.
     */
    public function createPersonalTeam(): Team
    {
        return $this->createTeam($this->name.'\'s Team', true);
    }

    /**
     * Leave a given team.
     */
    public function leaveTeam(Team $team): void
    {
        $this->teams()->detach($team);
    }

    /**
     * Add the user to a given team.
     */
    public function addToTeam(Team $team): void
    {
        if (! $this->belongsToTeam($team)) {
            $this->teams()->attach($team);
        }
    }

    /**
     * Remove the user from a given team.
     */
    public function removeFromTeam(Team $team): void
    {
        if ($this->belongsToTeam($team)) {
            $this->teams()->detach($team);
        }
    }

    /**
     * Check if the user has a personal team.
     */
    public function hasPersonalTeam(): bool
    {
        return $this->teams()->where('personal_team', true)->exists();
    }

    /**
     * Determine if the given team is the current team.
     */
    public function isCurrentTeam(Team $team): bool
    {
        return $team->id === $this->currentTeam->id;
    }

    /**
     * Get the current team of the user's context.
     */
    public function currentTeam(): BelongsTo
    {
        if (is_null($this->current_team_id) && $this->id) {
            $this->switchTeam($this->personalTeam());
        }

        return $this->belongsTo(Team::class, 'current_team_id');
    }

    /**
     * Switch the user's context to the given team.
     */
    public function switchTeam(Team $team): bool
    {
        if (! $this->belongsToTeam($team)) {
            return false;
        }

        $this->forceFill([
            'current_team_id' => $team->id,
        ])->save();

        $this->setRelation('currentTeam', $team);

        return true;
    }

    /**
     * Get all of the teams the user owns or belongs to.
     */
    public function allTeams(): Collection
    {
        return $this->ownedTeams->merge($this->teams)->sortBy('name');
    }

    /**
     * Get all of the teams the user owns.
     */
    public function ownedTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'user_id');
    }

    /**
     * Get all of the teams the user belongs to.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, Membership::class)
            ->withTimestamps()
            ->as('membership');
    }

    /**
     * Get the user's "personal" team.
     */
    public function personalTeam(): ?Team
    {
        return $this->ownedTeams->where('personal_team', true)->first();
    }

    /**
     * Create a new team for the user.
     */
    public function createTeam(string $name, ?bool $isPersonal = false): Team
    {
        return $this->teams()->create([
            'name' => $name,
            'user_id' => $this->id,
            'organisation_id' => $this->organisation_id,
            'personal_team' => $isPersonal,
        ]);
    }

    /**
     * Determine if the user owns the given team.
     */
    public function ownsTeam(Team $team): bool
    {
        if (is_null($team)) {
            return false;
        }

        return $this->id == $team->user_id;
    }

    /**
     * Determine if the user belongs to the given team.
     */
    public function belongsToTeam(Team $team): bool
    {
        if (is_null($team)) {
            return false;
        }

        return $this->ownsTeam($team)
            || $this->teams->contains(function ($t) use ($team) {
                return $t->id === $team->id;
            });
    }
}

<?php

namespace App\Policies;

use App\Models\Secret;
use App\Models\User;

class SecretPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Secret $secret): bool
    {
        return ! $secret->hasExpired();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(?User $user = null): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(?User $user, Secret $secret): bool
    {
        return true;
    }
}

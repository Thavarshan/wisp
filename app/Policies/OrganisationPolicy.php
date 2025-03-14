<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\Resource;
use App\Models\Organisation;
use App\Models\User;

class OrganisationPolicy extends Policy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(
            $this->do(Permission::VIEW_ANY, Resource::ORGANISATIONS)
        );
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Organisation $model): bool
    {
        return $user->isAssociatedWith($model) && $user->hasPermissionTo(
            $this->do(Permission::VIEW, Resource::ORGANISATIONS)
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(
            $this->do(Permission::CREATE, Resource::ORGANISATIONS)
        );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Organisation $model): bool
    {
        return $user->isAssociatedWith($model) && $user->hasPermissionTo(
            $this->do(Permission::UPDATE, Resource::ORGANISATIONS)
        );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Organisation $model): bool
    {
        return $user->hasPermissionTo(
            $this->do(Permission::DELETE, Resource::ORGANISATIONS)
        );
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Organisation $model): bool
    {
        return $user->hasPermissionTo(
            $this->do(Permission::DELETE, Resource::ORGANISATIONS)
        );
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Organisation $model): bool
    {
        return $user->hasPermissionTo(
            $this->do(Permission::DELETE, Resource::ORGANISATIONS)
        );
    }
}

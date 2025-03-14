<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\Resource;
use App\Models\Invitation;
use App\Models\User;

class InvitationPolicy extends Policy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(
            $this->do(Permission::VIEW_ANY, Resource::INVITATIONS)
        );
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invitation $model): bool
    {
        return $user->isAssociatedWith($model->organisation) && $user->hasPermissionTo(
            $this->do(Permission::VIEW, Resource::INVITATIONS)
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(
            $this->do(Permission::CREATE, Resource::INVITATIONS)
        );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Invitation $model): bool
    {
        return $user->isAssociatedWith($model->organisation) && $user->hasPermissionTo(
            $this->do(Permission::UPDATE, Resource::INVITATIONS)
        );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Invitation $model): bool
    {
        return $user->isAssociatedWith($model->organisation) && $user->hasPermissionTo(
            $this->do(Permission::DELETE, Resource::INVITATIONS)
        );
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Invitation $model): bool
    {
        return $user->isAssociatedWith($model->organisation) && $user->hasPermissionTo(
            $this->do(Permission::DELETE, Resource::INVITATIONS)
        );
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Invitation $model): bool
    {
        return $user->isAssociatedWith($model->organisation) && $user->hasPermissionTo(
            $this->do(Permission::DELETE, Resource::INVITATIONS)
        );
    }
}

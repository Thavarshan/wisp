<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\Resource;
use App\Models\Role;
use App\Models\User;

class RolePolicy extends Policy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(
            $this->do(Permission::VIEW_ANY, Resource::ROLES)
        );
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->hasPermissionTo(
            $this->do(Permission::VIEW, Resource::ROLES)
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(
            $this->do(Permission::CREATE, Resource::ROLES)
        );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        return $user->hasPermissionTo(
            $this->do(Permission::UPDATE, Resource::ROLES)
        );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        return $user->hasPermissionTo(
            $this->do(Permission::DELETE, Resource::ROLES)
        );
    }
}

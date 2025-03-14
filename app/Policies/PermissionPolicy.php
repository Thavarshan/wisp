<?php

namespace App\Policies;

use App\Enums\Permission as PermissionEnum;
use App\Enums\Resource;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class PermissionPolicy extends Policy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(
            $this->do(PermissionEnum::VIEW_ANY, Resource::PERMISSIONS)
        );
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo(
            $this->do(PermissionEnum::VIEW, Resource::PERMISSIONS)
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(
            $this->do(PermissionEnum::CREATE, Resource::PERMISSIONS)
        );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo(
            $this->do(PermissionEnum::UPDATE, Resource::PERMISSIONS)
        );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo(
            $this->do(PermissionEnum::DELETE, Resource::PERMISSIONS)
        );
    }
}

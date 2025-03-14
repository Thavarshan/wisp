<?php

namespace App\Traits;

use App\Enums\Permission as PermissionEnum;
use App\Enums\Role as RoleEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    /**
     * A user may have multiple roles.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Assign the given role to the user.
     */
    public function assignRole(
        RoleEnum|Role|array|string $role
    ): static {
        $role = $this->convertToRoleModel($role);
        $this->syncRoles($role, 'syncWithoutDetaching');

        return $this;
    }

    /**
     * Remove the given role from the user.
     */
    public function removeRole(
        RoleEnum|Role|array|string $role
    ): static {
        $role = $this->convertToRoleModel($role);
        $this->syncRoles($role, 'detach');

        return $this;
    }

    /**
     * Fetch the user's permissions.
     */
    public function permissions(): Collection
    {
        return $this->roles
            ->map
            ->permissions
            ->flatten()
            ->pluck('name')
            ->unique();
    }

    /**
     * Determine if the user has the given role.
     */
    public function hasRole(
        RoleEnum|Role|string $role
    ): bool {
        if ($this->roles->isEmpty()) {
            return false;
        }

        $role = $this->convertToRoleModel($role);

        return $this->checkRoles($role);
    }

    /**
     * Determine if this model has any of the given permissions through roles.
     */
    public function hasPermissionTo(
        PermissionEnum|Permission|string $permission
    ): bool {
        if ($this->roles->isEmpty()) {
            return false;
        }

        $permissionName = match (true) {
            $permission instanceof PermissionEnum => $permission->value,
            $permission instanceof Permission => $permission->name,
            is_string($permission) => $permission,
            default => null,
        };

        if (is_null($permissionName)) {
            return false;
        }

        return $this->roles
            ->map
            ->permissions
            ->flatten()
            ->pluck('name')
            ->contains($permissionName);
    }

    /**
     * Determine if the user has any of the given roles.
     */
    public function hasAnyRole(
        RoleEnum|Role|array|string $roles
    ): bool {
        $roles = $this->convertToRoleModel($roles);

        return $this->checkRoles($roles);
    }

    /**
     * Convert the given role(s) to a Role model instance(s).
     */
    protected function convertToRoleModel(
        RoleEnum|Role|array|string $roles
    ): Role|Collection {
        return match (true) {
            $roles instanceof RoleEnum => Role::where('name', $roles->value)->first(),
            $roles instanceof Role => $roles,
            is_array($roles) => Role::whereIn('name', $roles)->get(),
            is_string($roles) => Role::where('name', $roles)->first(),
            default => collect(),
        };
    }

    /**
     * Sync roles with the given method.
     */
    protected function syncRoles(Role|Collection $role, string $method): void
    {
        if ($role instanceof Collection) {
            $this->roles()->$method($role);
        } else {
            $this->roles()->$method([$role->id]);
        }
    }

    /**
     * Check roles.
     */
    protected function checkRoles(Role|Collection $role): bool
    {
        if ($role instanceof Collection) {
            return $this->roles
                ->pluck('id')
                ->intersect($role->pluck('id'))
                ->isNotEmpty();
        }

        return $this->roles->contains($role->id);
    }
}

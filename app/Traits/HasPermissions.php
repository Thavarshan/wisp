<?php

namespace App\Traits;

use App\Enums\Permission as PermissionEnum;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPermissions
{
    /**
     * A role may have many permissions.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Grant the given permission to the role.
     */
    public function givePermissionTo(Permission $permission): static
    {
        $this->permissions()->syncWithoutDetaching($permission);

        return $this;
    }

    /**
     * Revoke the given permission from the role.
     */
    public function revokePermissionTo(Permission $permission): static
    {
        $this->permissions()->detach($permission);

        return $this;
    }

    /**
     * Determine if the role has the given permission.
     */
    public function hasPermissionTo(Permission $permission): bool
    {
        return $this->permissions->contains('id', $permission->id);
    }

    /**
     * Determine if the role has the given permission by name.
     */
    public function hasPermissionByName(string $permissionName): bool
    {
        return $this->permissions->contains('name', $permissionName);
    }

    /**
     * Determine if the role has any permissions.
     */
    public function hasPermissions(): bool
    {
        return $this->permissions->isNotEmpty();
    }

    /**
     * Determine if the role has any of the given permissions.
     */
    public function hasAnyPermission(
        PermissionEnum|Permission|string ...$permissions
    ): bool {
        foreach ($permissions as $permission) {
            if ($this->hasPermissionTo(
                $this->convertToPermissionModel($permission)
            )) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the role has all of the given permissions.
     */
    public function hasAllPermissions(
        PermissionEnum|Permission|string ...$permissions
    ): bool {
        foreach ($permissions as $permission) {
            if (! $this->hasPermissionTo(
                $this->convertToPermissionModel($permission)
            )) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sync the given permissions to the role.
     */
    public function syncPermissions(array $permissions): static
    {
        $permissionModels = array_map(
            [$this, 'convertToPermissionModel'],
            $permissions
        );

        $this->permissions()->sync($permissionModels);

        return $this;
    }

    /**
     * Convert the given permission to a permission model.
     */
    protected function convertToPermissionModel(
        PermissionEnum|Permission|string $permission
    ): Permission {
        if ($permission instanceof Permission) {
            return $permission;
        }

        if ($permission instanceof PermissionEnum || is_string($permission)) {
            return Permission::where('name', $permission)->firstOrFail();
        }

        throw new \InvalidArgumentException('Invalid permission type or permission not found');
    }
}

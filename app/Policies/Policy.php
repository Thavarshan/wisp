<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\Resource;

abstract class Policy
{
    /**
     * Get the permission string for the given resource.
     */
    protected static function do(
        Permission $permission,
        Resource $resource
    ): string {
        return "{$permission->value}:{$resource->value}";
    }
}

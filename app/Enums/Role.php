<?php

namespace App\Enums;

use App\Enums\Traits\EnumValuesFetcher;

enum Role: string
{
    use EnumValuesFetcher;

    case SUPER_ADMIN = 'Super-Admin';
    case ADMIN = 'Admin';
    case MANAGER = 'Manager';
    case STAFF = 'Staff';
    case AGENT = 'Agent';
    case CLIENT = 'Client';
    case CLIENT_OWNER = 'Client-Owner';
    case CLIENT_MANAGER = 'Client-Manager';
    case CLIENT_STAFF = 'Client-Staff';

    /**
     * Get the default roles.
     */
    public static function defaults(?bool $asValues = false): array
    {
        $defaults = [
            self::ADMIN,
            self::MANAGER,
            self::STAFF,
        ];

        if ($asValues) {
            return array_map(fn ($role) => $role->value, $defaults);
        }

        return $defaults;
    }
}

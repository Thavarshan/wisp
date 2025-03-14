<?php

use App\Enums\Permission;
use App\Enums\Resource;
use App\Enums\Role;

return [
    Role::SUPER_ADMIN->value => [
        Resource::ROLES->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
            Permission::CREATE->value,
            Permission::UPDATE->value,
            Permission::DELETE->value,
        ],

        Resource::PERMISSIONS->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
            Permission::CREATE->value,
            Permission::UPDATE->value,
            Permission::DELETE->value,
        ],

        Resource::ORGANISATIONS->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
            Permission::CREATE->value,
            Permission::UPDATE->value,
            Permission::DELETE->value,
        ],

        Resource::INVITATIONS->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
            Permission::CREATE->value,
            Permission::UPDATE->value,
            Permission::DELETE->value,
        ],

        Resource::TEAMS->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
            Permission::CREATE->value,
            Permission::UPDATE->value,
            Permission::DELETE->value,
        ],

        Resource::USERS->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
            Permission::CREATE->value,
            Permission::UPDATE->value,
            Permission::DELETE->value,
        ],
    ],

    Role::ADMIN->value => [
        Resource::ROLES->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
            Permission::CREATE->value,
            Permission::UPDATE->value,
            Permission::DELETE->value,
        ],

        Resource::PERMISSIONS->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
            Permission::CREATE->value,
            Permission::UPDATE->value,
            Permission::DELETE->value,
        ],

        Resource::ORGANISATIONS->value => [
            Permission::VIEW->value,
            Permission::UPDATE->value,
        ],

        Resource::INVITATIONS->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
            Permission::CREATE->value,
            Permission::UPDATE->value,
            Permission::DELETE->value,
        ],

        Resource::TEAMS->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
            Permission::CREATE->value,
            Permission::UPDATE->value,
            Permission::DELETE->value,
        ],

        Resource::USERS->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
            Permission::CREATE->value,
            Permission::UPDATE->value,
            Permission::DELETE->value,
        ],
    ],

    Role::MANAGER->value => [
        Resource::ROLES->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
        ],

        Resource::PERMISSIONS->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
        ],

        Resource::ORGANISATIONS->value => [
            Permission::VIEW->value,
            Permission::UPDATE->value,
        ],

        Resource::INVITATIONS->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
            Permission::CREATE->value,
            Permission::UPDATE->value,
            Permission::DELETE->value,
        ],

        Resource::TEAMS->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
            Permission::CREATE->value,
            Permission::UPDATE->value,
        ],

        Resource::USERS->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
            Permission::CREATE->value,
            Permission::UPDATE->value,
        ],
    ],

    Role::STAFF->value => [
        Resource::ROLES->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
        ],

        Resource::PERMISSIONS->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
        ],

        Resource::ORGANISATIONS->value => [
            Permission::VIEW->value,
        ],

        Resource::INVITATIONS->value => [],

        Resource::TEAMS->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
        ],

        Resource::USERS->value => [
            Permission::VIEW_ANY->value,
            Permission::VIEW->value,
            Permission::UPDATE->value,
        ],
    ],
];

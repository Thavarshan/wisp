<?php

namespace App\Enums;

enum Resource: string
{
    case ROLES = 'roles';
    case PERMISSIONS = 'permissions';
    case ORGANISATIONS = 'organisations';
    case INVITATIONS = 'invitations';
    case TEAMS = 'teams';
    case USERS = 'users';
}

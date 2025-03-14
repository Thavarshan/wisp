<?php

namespace App\Enums;

use App\Traits\EnumValuesFetcher;

enum Permission: string
{
    use EnumValuesFetcher;

    case VIEW_ANY = 'viewAny';
    case VIEW = 'view';
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
}

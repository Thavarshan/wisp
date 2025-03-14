<?php

namespace App\Enums\Traits;

use Illuminate\Support\Collection;

trait EnumValuesFetcher
{
    /**
     * Get all the values of the enum as an array.
     *
     *
     * @return \Illuminate\Support\Collection|array<string>
     */
    public static function all(?bool $asCollection = false): Collection|array
    {
        $results = array_map(
            fn (self $enumCase): string => $enumCase->value,
            self::cases()
        );

        return $asCollection ? collect($results) : $results;
    }
}

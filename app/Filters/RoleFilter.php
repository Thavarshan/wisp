<?php

namespace App\Filters;

use Filterable\Filter;
use Illuminate\Database\Eloquent\Builder;

class RoleFilter extends Filter
{
    /**
     * Registered filters to operate upon.
     *
     * @var array<string>
     */
    protected array $filters = ['filter'];

    /**
     * Filter the query by a given attribute value.
     *
     * @param string $value
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function filter(string $value): Builder
    {
        return $this->builder->where('column', $value);
    }
}

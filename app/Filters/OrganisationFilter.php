<?php

namespace App\Filters;

use Filterable\Filter;
use Illuminate\Database\Eloquent\Builder;

class OrganisationFilter extends Filter
{
    /**
     * Registered filters to operate upon.
     *
     * @var array<string>
     */
    protected array $filters = ['filter'];

    /**
     * Filter the query by a given attribute value.
     */
    protected function filter(string $value): Builder
    {
        return $this->builder->where('column', $value);
    }
}

<?php

namespace App\Filters;

use Filterable\Filter;
use Illuminate\Database\Eloquent\Builder;

class UserFilter extends Filter
{
    /**
     * Registered filters to operate upon.
     *
     * @var array<string>
     */
    protected array $filters = [
        'first_name',
        'last_name',
        'email',
        'username',
        'phone',
        'date_of_birth',
        'created_at',
        'verified',
    ];

    /**
     * Filter the query by a given first name.
     */
    protected function firstName(string $value): Builder
    {
        return $this->builder->where('first_name', $value);
    }

    /**
     * Filter the query by a given last name.
     */
    protected function lastName(string $value): Builder
    {
        return $this->builder->where('last_name', $value);
    }

    /**
     * Filter the query by a given email.
     */
    protected function email(string $value): Builder
    {
        return $this->builder->where('email', $value);
    }

    /**
     * Filter the query by a given username.
     */
    protected function username(string $value): Builder
    {
        return $this->builder->where('username', $value);
    }

    /**
     * Filter the query by a given phone.
     */
    protected function phone(string $value): Builder
    {
        return $this->builder->where('phone', $value);
    }

    /**
     * Filter the query by a given date of birth.
     */
    protected function dateOfBirth(string $value): Builder
    {
        return $this->builder->whereDate('date_of_birth', $value);
    }

    /**
     * Filter the query by a given creation date.
     */
    protected function createdAt(string $value): Builder
    {
        return $this->builder->whereDate('created_at', $value);
    }

    /**
     * Filter the query by verification status.
     */
    protected function verified(): Builder
    {
        return $this->builder->whereNotNull('email_verified_at');
    }
}

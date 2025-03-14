<?php

namespace App\Models\Scopes;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class BelongsToOrganisation implements Scope
{
    use UsesTenantConnection;

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Organisation::checkCurrent()) {
            $builder->where('organisation_id', Organisation::current()->id);
        }
    }
}

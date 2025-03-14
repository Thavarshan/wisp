<?php

namespace App\Models\Scopes;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class BelongsToOrganisationRelatedRoles implements Scope
{
    use UsesTenantConnection;

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (! Organisation::checkCurrent()) {
            return;
        }

        $builder->whereHas('roles', function (Builder $query) {
            $query->where('organisation_id', Organisation::current()->id);
        });
    }
}

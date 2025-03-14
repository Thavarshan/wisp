<?php

namespace App\Models;

use App\Models\Scopes\BelongsToOrganisationRelatedRoles;
use Filterable\Interfaces\Filterable;
use Filterable\Traits\Filterable as HasFilters;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ScopedBy([BelongsToOrganisationRelatedRoles::class])]
class Permission extends Model implements Filterable
{
    use HasFactory, HasFilters;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * An ability may have many roles.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}

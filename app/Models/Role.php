<?php

namespace App\Models;

use App\Enums\Role as RoleType;
use App\Models\Scopes\BelongsToOrganisation;
use App\Observers\RoleObserver;
use App\Traits\HasOrganisationRelationship;
use App\Traits\HasPermissions;
use Filterable\Interfaces\Filterable;
use Filterable\Traits\Filterable as HasFilters;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

#[ObservedBy(RoleObserver::class)]
#[ScopedBy([BelongsToOrganisation::class])]
class Role extends Model implements Filterable
{
    use HasFactory, HasFilters, HasOrganisationRelationship, HasPermissions, HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'organisation_id',
    ];

    /**
     * Get all users with this role assigned.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Find role by name.
     */
    public static function findByName(string $role): static
    {
        return static::whereName($role)->firstOrFail();
    }

    /**
     * Check if the role is named as the given name.
     */
    public function namedAs(RoleType|string $name): bool
    {
        $name = $name instanceof RoleType ? $name->value : $name;

        return $this->name === $name;
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
}

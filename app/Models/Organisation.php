<?php

namespace App\Models;

use App\Enums\DefaultData;
use App\Models\Traits\HasApiAccess;
use App\Models\Traits\HasUid;
use App\Observers\OrganisationObserver;
use Filterable\Interfaces\Filterable;
use Filterable\Traits\Filterable as HasFilters;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\Models\Concerns\ImplementsTenant;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

#[ObservedBy(OrganisationObserver::class)]
class Organisation extends Model implements Filterable, IsTenant
{
    use Billable, HasApiAccess, HasFactory, HasFilters, HasSlug, HasUid, ImplementsTenant, UsesLandlordConnection;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'uid',
        'name',
        'slug',
        'phone',
        'email',
        'website',
        'logo',
    ];

    /**
     * Get the default organisation.
     */
    public static function getDefault(): static
    {
        return static::firstOrCreate([
            'name' => DefaultData::ORGANISATION->value,
        ]);
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

    /**
     * Get the users for the organisation.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the roles for the organisation.
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * Get the teams for the organisation.
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Associate a user or team with the organisation.
     */
    public function associate(User|Team $association): User|Team
    {
        if ($association instanceof User) {
            return $this->users()->save($association);
        }

        return $this->teams()->save($association);
    }
}

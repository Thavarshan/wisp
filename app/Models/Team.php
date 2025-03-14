<?php

namespace App\Models;

use App\Models\Scopes\BelongsToOrganisation;
use App\Observers\TeamObserver;
use App\Traits\HasOrganisationRelationship;
use App\Traits\HasUid;
use Filterable\Interfaces\Filterable;
use Filterable\Traits\Filterable as HasFilters;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

#[ObservedBy(TeamObserver::class)]
#[ScopedBy([BelongsToOrganisation::class])]
class Team extends Model implements Filterable
{
    use HasFactory, HasFilters, HasOrganisationRelationship, HasSlug, HasUid;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'uid',
        'name',
        'slug',
        'description',
        'user_id',
        'organisation_id',
    ];

    /**
     * Get the owner of the team.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the members of the team.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'team_user',
            'team_id',
            'user_id'
        );
    }

    /**
     * Purge the team from the database.
     */
    public function purge(): void
    {
        $this->owner()
            ->where('current_team_id', $this->id)
            ->update(['current_team_id' => null]);

        $this->members()
            ->where('current_team_id', $this->id)
            ->update(['current_team_id' => null]);

        $this->members()->detach();

        $this->delete();
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

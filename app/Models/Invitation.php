<?php

namespace App\Models;

use App\Models\Scopes\BelongsToOrganisation;
use App\Models\Traits\HasOrganisationRelationship;
use App\Models\Traits\HasUid;
use Filterable\Interfaces\Filterable;
use Filterable\Traits\Filterable as HasFilters;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ScopedBy([BelongsToOrganisation::class])]
class Invitation extends Model implements Filterable
{
    use HasFactory, HasFilters, HasOrganisationRelationship, HasUid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'email',
        'role',
        'organisation_id',
    ];

    /**
     * Determine if an invitation with the given email exists.
     */
    public static function withEmailExists(string $email): bool
    {
        return static::where('email', $email)->exists();
    }
}

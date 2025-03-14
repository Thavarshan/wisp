<?php

namespace App\Models;

use App\Models\Scopes\BelongsToOrganisation;
use App\Models\Traits\HasOrganisationRelationship;
use App\Models\Traits\HasRoles;
use App\Models\Traits\HasTeams;
use App\Models\Traits\HasUid;
use App\Observers\UserObserver;
use Filterable\Interfaces\Filterable;
use Filterable\Traits\Filterable as HasFilters;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

#[ObservedBy(UserObserver::class)]
#[ScopedBy([BelongsToOrganisation::class])]
class User extends Authenticatable implements Filterable, MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasFilters, HasOrganisationRelationship, HasRoles, HasTeams, HasUid, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uid',
        'first_name',
        'last_name',
        'username',
        'email',
        'phone',
        'date_of_birth',
        'password',
        'about',
        'meta',
        'organisation_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be appended to the model.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'name',
    ];

    /**
     * Determine if the user is associated with the given organisation or team.
     */
    public static function createFromInvitation(
        Invitation $invitation,
        array $data
    ): static {
        $user = static::create(array_merge($data, [
            'email' => $invitation->email,
            'organisation_id' => $invitation->organisation_id,
            'email_verified_at' => now(),
        ]));

        $user->assignRole($invitation->role);

        $invitation->delete();

        return $user;
    }

    /**
     * Get the user's full name.
     */
    public function name(): Attribute
    {
        return Attribute::make(
            fn () => $this->first_name.' '.$this->last_name,
        );
    }

    /**
     * Determine if the user is associated with the given organisation or team.
     */
    public function isAssociatedWith(Organisation|Team $association): bool
    {
        if ($association instanceof Organisation) {
            return $this->organisation->is($association);
        }

        if ($this->ownsTeam($association)) {
            return true;
        }

        return $this->teams->contains($association);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'meta' => 'array',
        ];
    }

    /**
     * Get the default guard name used by roles and permissions.
     */
    protected function getDefaultGuardName(): string
    {
        return 'api';
    }
}

<?php

namespace App\Models;

use App\Observers\SecretObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(SecretObserver::class)]
class Secret extends Model
{
    /** @use HasFactory<\Database\Factories\SecretFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'uid',
        'content',
        'password',
        'expired_at',
        'viewed_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Find a secret by its UID.
     */
    public static function findByUid(string $uid): ?self
    {
        return self::where('uid', $uid)->first();
    }

    /**
     * Determine if the secret is available.
     */
    public static function isAvailable(string $uid): bool
    {
        return self::where('uid', $uid)->exists();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uid';
    }

    /**
     * Get the share link for the secret.
     */
    public function getShareLink(): string
    {
        return route('secrets.show', $this);
    }

    /**
     * Determine if the secret has expired.
     */
    public function hasExpired(): bool
    {
        return $this->expired_at && $this->expired_at->isPast();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expired_at' => 'datetime',
            'viewed_at' => 'datetime',
        ];
    }
}

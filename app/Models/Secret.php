<?php

namespace App\Models;

use Database\Factories\SecretFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class Secret extends Model
{
    public const MAX_CONTENT_LENGTH = 10000;

    public const MAX_PASSWORD_LENGTH = 255;

    /**
     * @use HasFactory<SecretFactory>
     */
    use HasFactory, MassPrunable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'access_token_hash',
        'revocation_token_hash',
        'content',
        'password',
        'expired_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<string>
     */
    protected $hidden = [
        'access_token_hash',
        'revocation_token_hash',
        'content',
        'password',
    ];

    /**
     * Scope a query to a hashed access token.
     *
     * @param  Builder<self>  $query  The secret query builder.
     * @param  string  $token  The raw access token.
     * @return Builder<self> The constrained query builder.
     */
    public function scopeWithAccessToken(
        Builder $query,
        string $token,
    ): Builder {
        return $query->where('access_token_hash', self::hashToken($token));
    }

    /**
     * Hash a token before it is compared with persisted secret data.
     *
     * @param  string  $token  The raw token.
     * @return string The SHA-256 token hash.
     */
    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * Determine if the secret has expired.
     */
    public function hasExpired(): bool
    {
        return $this->expired_at->isPast();
    }

    /**
     * Determine if the secret has a password.
     */
    public function hasPassword(): bool
    {
        return filled($this->password);
    }

    /**
     * Build the query used by Laravel's mass-pruning command.
     *
     * @return Builder<self> Secrets that have reached their expiration time.
     */
    public function prunable(): Builder
    {
        return static::query()->where('expired_at', '<=', now());
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content' => 'encrypted',
            'password' => 'hashed',
            'expired_at' => 'datetime',
        ];
    }
}

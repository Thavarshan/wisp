<?php

namespace App\Models;

use Database\Factories\SecretFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'access_token_hash',
    'revocation_token_hash',
    'content',
    'password',
    'expired_at',
])]
#[Hidden([
    'access_token_hash',
    'revocation_token_hash',
    'content',
    'password',
])]
class Secret extends Model
{
    public const MAX_CONTENT_LENGTH = 10000;

    public const MAX_PASSWORD_BYTES = 72;

    /**
     * @use HasFactory<SecretFactory>
     */
    use HasFactory, MassPrunable;

    /**
     * Scope a query to a hashed access token.
     *
     * @param  Builder<self>  $query  The secret query builder.
     * @param  string  $token  The raw access token.
     * @return Builder<self> The constrained query builder.
     */
    #[Scope]
    protected function withSecretId(
        Builder $query,
        string $secretId,
    ): void {
        $query->where('access_token_hash', $secretId);
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
    #[\Override]
    protected function casts(): array
    {
        return [
            'content' => 'encrypted',
            'password' => 'hashed',
            'expired_at' => 'datetime',
        ];
    }
}

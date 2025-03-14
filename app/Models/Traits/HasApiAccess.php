<?php

namespace App\Models\Traits;

use App\Models\Token;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Passport\Passport;
use Laravel\Passport\TransientToken;

trait HasApiAccess
{
    /**
     * The current access token for the authentication organisation.
     */
    protected Token|TransientToken|null $accessToken;

    /**
     * Get all of the organisation's registered OAuth clients.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Passport::clientModel(), 'organisation_id');
    }

    /**
     * Get all of the access tokens for the organisation.
     */
    public function tokens(): HasMany
    {
        return $this->hasMany(Passport::tokenModel(), 'organisation_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the current access token being used by the organisation.
     */
    public function token(): Token|TransientToken|null
    {
        return $this->accessToken;
    }

    /**
     * Determine if the current API token has a given scope.
     */
    public function tokenCan(string $scope): bool
    {
        return $this->accessToken ? $this->accessToken->can($scope) : false;
    }

    /**
     * Create a new personal access token for the organisation.
     */
    public function createToken(
        $name,
        array $scopes = []
    ): Token|TransientToken|null {
        $client = $this->clients()->firstOrCreate([
            'name' => $name ?? $this->name,
            'redirect' => '',
            'personal_access_client' => true,
            'password_client' => false,
            'revoked' => false,
        ]);

        return tap($this->tokens()->create([
            'client_id' => $client->getKey(),
            'name' => $name ?? $this->name,
            'scopes' => $scopes,
            'revoked' => false,
        ]), function ($token) {
            $this->withAccessToken($token);
        });
    }

    /**
     * Revoke the given token.
     */
    public function revokeToken(Token $token): void
    {
        $token->revoke();
    }

    /**
     * Set the current access token for the organisation.
     */
    public function withAccessToken(Token|TransientToken|null $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }
}

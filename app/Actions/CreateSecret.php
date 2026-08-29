<?php

namespace App\Actions;

use App\Enums\ExpirationOption;
use App\Models\Secret;

class CreateSecret
{
    /** @return array{access_token: string, revocation_token: string, share_url: string, expires_at: string, expiration: array{value: string, label: string}} */
    public function handle(array $data): array
    {
        $accessToken = bin2hex(random_bytes(32));
        $revocationToken = bin2hex(random_bytes(32));
        $expiration = ExpirationOption::from($data['expiration']);

        $secret = Secret::create([
            'access_token_hash' => Secret::hashToken($accessToken),
            'revocation_token_hash' => Secret::hashToken($revocationToken),
            'content' => $data['content'],
            'password' => filled($data['password'] ?? null) ? $data['password'] : null,
            'expired_at' => $expiration->expiresAt(),
        ]);

        return [
            'access_token' => $accessToken,
            'revocation_token' => $revocationToken,
            'share_url' => route('secrets.show', ['token' => $accessToken]),
            'expires_at' => $secret->expired_at->toIso8601String(),
            'expiration' => ['value' => $expiration->value, 'label' => $expiration->label()],
        ];
    }
}

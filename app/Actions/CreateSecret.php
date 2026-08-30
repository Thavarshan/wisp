<?php

namespace App\Actions;

use App\Enums\ExpirationOption;
use App\Models\Secret;

class CreateSecret
{
    /**
     * Create a secret and return its one-time credentials.
     *
     * @param array{
     *     content: string,
     *     expiration: string,
     *     password?: string|null
     * } $data
     * @return array{
     *     secret_id: string,
     *     revocation_token: string,
     *     share_url: string,
     *     expires_at: string,
     *     expiration: array{value: string, label: string}
     * }
     */
    public function handle(array $data): array
    {
        $accessToken = bin2hex(random_bytes(32));
        $revocationToken = bin2hex(random_bytes(32));
        $expiration = ExpirationOption::from($data['expiration']);

        $secret = Secret::create([
            'access_token_hash' => Secret::hashToken($accessToken),
            'revocation_token_hash' => Secret::hashToken($revocationToken),
            'content' => $data['content'],
            'password' => filled($data['password'] ?? null)
                ? $data['password']
                : null,
            'expired_at' => $expiration->expiresAt(),
        ]);

        return [
            'secret_id' => $secret->access_token_hash,
            'revocation_token' => $revocationToken,
            'share_url' => rtrim(config('app.url'), '/').route(
                'secrets.show',
                ['secret_id' => $secret->access_token_hash],
                false,
            ).'#'.$accessToken,
            'expires_at' => $secret->expired_at->toIso8601String(),
            'expiration' => [
                'value' => $expiration->value,
                'label' => $expiration->label(),
            ],
        ];
    }
}

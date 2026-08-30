<?php

namespace App\Actions;

use App\Models\Secret;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RevealSecret
{
    /**
     * Reveal and permanently delete a secret in one transaction.
     *
     * @param  string  $secretId  The public secret identifier from the URL.
     * @param  mixed  $accessToken  The raw access token from the URL fragment.
     * @param  string|null  $password  The password supplied by the recipient.
     * @return string The decrypted secret content.
     */
    public function handle(
        string $secretId,
        mixed $accessToken,
        ?string $password = null,
    ): string {
        return DB::transaction(
            function () use ($secretId, $accessToken, $password): string {
                $secret = Secret::query()
                    ->withSecretId($secretId)
                    ->lockForUpdate()
                    ->first();

                abort_unless(
                    $secret
                        && is_string($accessToken)
                        && preg_match('/\A[0-9a-f]{64}\z/', $accessToken)
                        && hash_equals(
                            $secret->access_token_hash,
                            Secret::hashToken($accessToken),
                        ),
                    404,
                );
                abort_if($secret->hasExpired(), 410, 'Secret has expired.');

                if ($secret->hasPassword()) {
                    $hasValidPassword = is_string($password)
                        && Hash::check($password, $secret->password);

                    if (! $hasValidPassword) {
                        throw ValidationException::withMessages([
                            'password' => 'The provided password is incorrect.',
                        ]);
                    }
                }

                $content = $secret->content;
                $secret->delete();

                return $content;
            },
            attempts: 3,
        );
    }
}

<?php

namespace App\Actions;

use App\Models\Secret;
use Illuminate\Support\Facades\DB;

class RevokeSecret
{
    /**
     * Revoke and permanently delete a secret after token verification.
     *
     * @param  string  $accessToken  The raw access token from the share URL.
     * @param  string  $revocationToken  The private revocation token.
     */
    public function handle(string $accessToken, string $revocationToken): void
    {
        DB::transaction(function () use ($accessToken, $revocationToken): void {
            $secret = Secret::query()
                ->withAccessToken($accessToken)
                ->lockForUpdate()
                ->first();
            abort_unless($secret, 404);

            $presentedHash = Secret::hashToken($revocationToken);
            abort_unless(
                hash_equals($secret->revocation_token_hash, $presentedHash),
                404,
            );

            $secret->delete();
        }, attempts: 3);
    }
}

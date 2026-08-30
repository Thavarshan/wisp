<?php

namespace App\Actions;

use App\Models\Secret;
use Illuminate\Support\Facades\DB;

class RevokeSecret
{
    /**
     * Revoke and permanently delete a secret after token verification.
     *
     * @param  string  $secretId  The public secret identifier from the URL.
     * @param  string  $revocationToken  The private revocation token.
     */
    public function handle(string $secretId, string $revocationToken): void
    {
        DB::transaction(function () use ($secretId, $revocationToken): void {
            $secret = Secret::query()
                ->withSecretId($secretId)
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

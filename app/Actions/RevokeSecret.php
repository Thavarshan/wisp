<?php

namespace App\Actions;

use App\Models\Secret;
use Illuminate\Support\Facades\DB;

class RevokeSecret
{
    public function handle(string $accessToken, string $revocationToken): void
    {
        DB::transaction(function () use ($accessToken, $revocationToken): void {
            $secret = Secret::query()->withAccessToken($accessToken)->lockForUpdate()->first();
            abort_unless($secret, 404);

            $presentedHash = Secret::hashToken($revocationToken);
            abort_unless(hash_equals($secret->revocation_token_hash, $presentedHash), 404);

            $secret->delete();
        }, attempts: 3);
    }
}

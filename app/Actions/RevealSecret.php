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
     * @param  string  $accessToken  The raw access token from the share URL.
     * @param  string|null  $password  The password supplied by the recipient.
     * @return string The decrypted secret content.
     */
    public function handle(
        string $accessToken,
        ?string $password = null,
    ): string {
        return DB::transaction(
            function () use ($accessToken, $password): string {
                $secret = Secret::query()
                    ->withAccessToken($accessToken)
                    ->lockForUpdate()
                    ->first();

                abort_unless($secret, 404);
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

<?php

namespace App\Http\Controllers;

use App\Actions\CreateSecret;
use App\Actions\RevealSecret;
use App\Actions\RevokeSecret;
use App\Http\Requests\RevealSecretRequest;
use App\Http\Requests\RevokeSecretRequest;
use App\Http\Requests\StoreSecretRequest;
use App\Models\Secret;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class SecretController extends Controller
{
    public function store(StoreSecretRequest $request, CreateSecret $createSecret): JsonResponse
    {
        $result = $createSecret->handle($request->validated());

        return response()->json($result, Response::HTTP_CREATED)
            ->withHeaders(['Cache-Control' => 'no-store', 'X-Robots-Tag' => 'noindex, noarchive']);
    }

    public function show(string $token): InertiaResponse
    {
        $secret = Secret::query()->withAccessToken($token)->first();

        abort_unless($secret, 404);
        abort_if($secret->hasExpired(), 410, 'Secret has expired.');

        return Inertia::render('Secret', [
            'token' => $token,
            'has_password' => $secret->hasPassword(),
            'expired_at' => $secret->expired_at->toIso8601String(),
        ]);
    }

    public function reveal(RevealSecretRequest $request, RevealSecret $revealSecret, string $token): JsonResponse
    {
        $content = $revealSecret->handle($token, $request->validated('password'));

        return response()->json(['content' => $content])
            ->withHeaders(['Cache-Control' => 'no-store', 'Pragma' => 'no-cache', 'X-Robots-Tag' => 'noindex, noarchive']);
    }

    public function revoke(RevokeSecretRequest $request, RevokeSecret $revokeSecret, string $token): Response
    {
        $revokeSecret->handle($token, $request->validated('revocation_token'));

        return response()->noContent()->withHeaders(['Cache-Control' => 'no-store']);
    }
}

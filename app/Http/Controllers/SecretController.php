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
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class SecretController extends Controller
{
    /**
     * Create a secret and return its private credentials.
     *
     * @return JsonResponse The secret credentials with no-store headers.
     */
    #[Middleware('throttle:secret-creation')]
    public function store(
        StoreSecretRequest $request,
        CreateSecret $createSecret,
    ): JsonResponse {
        $result = $createSecret->handle($request->validated());

        return response()
            ->json($result, Response::HTTP_CREATED)
            ->withHeaders([
                'Cache-Control' => 'no-store',
                'X-Robots-Tag' => 'noindex, noarchive',
            ]);
    }

    /**
     * Render the reveal page or return a JSON availability error.
     *
     * @return InertiaResponse|JsonResponse The reveal page or error response.
     */
    #[Middleware('throttle:secret-access')]
    public function show(
        Request $request,
        string $secretId,
    ): InertiaResponse|JsonResponse {
        $secret = Secret::query()->withSecretId($secretId)->first();

        if (! $secret) {
            return $this->unavailable(
                $request,
                Response::HTTP_NOT_FOUND,
                'This secret is no longer available.',
            );
        }

        if ($secret->hasExpired()) {
            return $this->unavailable(
                $request,
                Response::HTTP_GONE,
                'This secret has expired.',
            );
        }

        return Inertia::render('Secret', [
            'secret_id' => $secretId,
            'has_password' => $secret->hasPassword(),
            'expired_at' => $secret->expired_at->toIso8601String(),
        ]);
    }

    /**
     * Return a safe unavailable response for browser or JSON callers.
     *
     * @return InertiaResponse|JsonResponse The error response, or aborts.
     */
    private function unavailable(
        Request $request,
        int $status,
        string $message,
    ): InertiaResponse|JsonResponse {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        abort($status, $message);
    }

    /**
     * Reveal and consume a secret after validating its optional password.
     *
     * @return JsonResponse The decrypted content with no-store headers.
     */
    #[Middleware('throttle:secret-reveal-ip')]
    #[Middleware('throttle:secret-reveal-secret')]
    public function reveal(
        RevealSecretRequest $request,
        RevealSecret $revealSecret,
        string $secretId,
    ): JsonResponse {
        $content = $revealSecret->handle(
            $secretId,
            $request->validated('access_token'),
            $request->validated('password'),
        );

        return response()->json(['content' => $content])
            ->withHeaders([
                'Cache-Control' => 'no-store',
                'Pragma' => 'no-cache',
                'X-Robots-Tag' => 'noindex, noarchive',
            ]);
    }

    /**
     * Revoke a secret after validating its private revocation token.
     *
     * @return Response An empty no-content response.
     */
    #[Middleware('throttle:secret-revocation')]
    public function revoke(
        RevokeSecretRequest $request,
        RevokeSecret $revokeSecret,
        string $secretId,
    ): Response {
        $revokeSecret->handle($secretId, $request->validated('revocation_token'));

        return response()->noContent()
            ->withHeaders(['Cache-Control' => 'no-store']);
    }
}

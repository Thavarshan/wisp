<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSecretRequest;
use App\Models\Secret;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class SecretController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('secure.secret', only: ['show']),
        ];
    }

    /**
     * Store a newly created secret.
     */
    public function store(StoreSecretRequest $request): RedirectResponse
    {
        $secret = Secret::create($request->getValidatedData());

        return redirect()->route('secrets.share', ['secret' => $secret->uid]);
    }

    /**
     * Display the specified secret.
     */
    public function show(Secret $secret): InertiaResponse|Response
    {
        return Inertia::render('Secret', [
            'secret' => Crypt::decrypt($secret->content),
            'has_password' => $secret->hasPassword(),
        ]);
    }

    /**
     * Destroy the specified secret.
     */
    public function destroy(
        Request $request,
        Secret $secret
    ): RedirectResponse|Response {
        $secret->delete();

        if ($request->wantsJson()) {
            return response()->noContent();
        }

        return redirect()->route('home');
    }
}

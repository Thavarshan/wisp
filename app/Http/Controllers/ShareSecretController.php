<?php

namespace App\Http\Controllers;

use App\Models\Secret;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ShareSecretController extends Controller
{
    /**
     * Show the share secret page with the secret link.
     */
    public function __invoke(Request $request): InertiaResponse
    {
        $secret = Secret::findByUid($request->query('secret'));

        return Inertia::render('Share', [
            'link' => $secret->getShareLink(),
            'expired_at' => $secret->expired_at->diffForHumans(),
        ]);
    }
}

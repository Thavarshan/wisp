<?php

use App\Enums\ExpirationOption;
use App\Http\Controllers\SecretController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'expiration_options' => ExpirationOption::options(),
    ]);
})->name('home');

Route::post('secrets', [SecretController::class, 'store'])
    ->name('secrets.store')
    ->middleware('throttle:secret-creation');

Route::get('secrets/{token}', [SecretController::class, 'show'])
    ->name('secrets.show')
    ->middleware('throttle:secret-access');

Route::post('secrets/{token}/reveal', [SecretController::class, 'reveal'])
    ->name('secrets.reveal')
    ->middleware(['throttle:secret-reveal-ip', 'throttle:secret-reveal-secret']);

Route::delete('secrets/{token}', [SecretController::class, 'revoke'])
    ->name('secrets.revoke')
    ->middleware('throttle:secret-revocation');

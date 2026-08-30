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
    ->name('secrets.store');

Route::get('secrets/{token}', [SecretController::class, 'show'])
    ->name('secrets.show');

Route::post('secrets/{token}/reveal', [SecretController::class, 'reveal'])
    ->name('secrets.reveal');

Route::delete('secrets/{token}', [SecretController::class, 'revoke'])
    ->name('secrets.revoke');

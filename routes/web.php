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

Route::get('secrets/{secret_id}', [SecretController::class, 'show'])
    ->where('secret_id', '[0-9a-f]{64}')
    ->name('secrets.show');

Route::post('secrets/{secret_id}/reveal', [SecretController::class, 'reveal'])
    ->where('secret_id', '[0-9a-f]{64}')
    ->name('secrets.reveal');

Route::delete('secrets/{secret_id}', [SecretController::class, 'revoke'])
    ->where('secret_id', '[0-9a-f]{64}')
    ->name('secrets.revoke');

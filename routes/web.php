<?php

use App\Http\Controllers\SecretController;
use App\Http\Controllers\ShareSecretController;
use App\Http\Controllers\ValidateSecretPasswordController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::resource('secrets', SecretController::class)
    ->only(['store', 'show'])
    ->middleware('throttle:60,1'); // Limit to 60 requests per minute per IP

Route::delete('secrets/{secret}', [SecretController::class, 'destroy'])
    ->name('secrets.destroy')
    ->middleware('throttle:30,1'); // Limit deletions to 30 per minute

Route::get('share', ShareSecretController::class)
    ->middleware(['secure.secret', 'throttle:120,1'])
    ->name('secrets.share');

Route::post('secrets/{secret}/password', ValidateSecretPasswordController::class)
    ->middleware(['secure.secret', 'throttle:10,1']) // More restrictive for password attempts
    ->name('secrets.password');

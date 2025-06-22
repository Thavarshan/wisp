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
    ->only(['store', 'show']);

Route::delete('secrets/{secret}/destroy', [SecretController::class, 'destroy'])
    ->name('secrets.destroy');

Route::get('share', ShareSecretController::class)
    ->middleware('secure.secret')
    ->name('secrets.share');

Route::post('secrets/{secret}/password', ValidateSecretPasswordController::class)
    ->middleware('secure.secret')
    ->name('secrets.password');

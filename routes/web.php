<?php

use App\Http\Controllers\SecretController;
use App\Http\Controllers\ShareSecretController;
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

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

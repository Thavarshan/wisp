<?php

use App\Http\Controllers\EmailVerificationNotificationController;
use App\Http\Controllers\VerifyEmailController;
use Illuminate\Support\Facades\Route;

$verificationLimiter = config('auth.limiters.verification', '6,1');

Route::group([
    'middleware' => ['auth:api', 'tenant'],
], function () use ($verificationLimiter) {
    Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:'.$verificationLimiter])
        ->name('verification.verify');

    Route::post('/email/verification-notification', EmailVerificationNotificationController::class)
        ->middleware(['throttle:'.$verificationLimiter])
        ->name('verification.send');
});

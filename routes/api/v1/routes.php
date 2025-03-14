<?php

use App\Enums\App;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'as' => sprintf('%s.', App::API_VERSION->value),
], function () {
    route_paths('common/auth');

    Route::group([
        'middleware' => ['auth:api', 'tenant'],
    ], function () {
        Route::get('/user', function (Request $request) {
            return response()->json(new UserResource($request->user()));
        });

        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('teams', TeamController::class);
        Route::apiResource('organisations', OrganisationController::class);
        Route::apiResource('invitations', InvitationController::class);
    });

    Route::post('/onboarding', OnboardingController::class)
        ->middleware('client')
        ->name('onboarding');
});

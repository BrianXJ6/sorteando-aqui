<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;

Route::name('api.')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Auth routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->name('auth.')->group(function () {
        // Users routes
        Route::prefix('users')->name('users.')->controller(UserAuthController::class)->group(function () {
            // Guest
            Route::middleware(['guest:sanctum'])->group(function () {
                Route::post('signin', 'signInApi')->name('signin-api');
            });
            // Authenticated
            Route::middleware(['auth:sanctum'])->group(function () {
                Route::post('signout', 'signOut')->name('signout-api');
            });
        });
    });
});

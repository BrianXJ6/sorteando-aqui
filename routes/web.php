<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;

Route::get('/', fn () => 'Home Page')->name('home');

/*
|--------------------------------------------------------------------------
| Action routes
|--------------------------------------------------------------------------
*/
Route::prefix('actions/auth')->name('actions.auth.')->group(function () {
    // Users routes
    Route::prefix('users')->name('users.')->controller(UserAuthController::class)->group(function () {
        // Guest
        Route::middleware(['guest:user'])->group(function () {
            Route::post('signin', 'signInWeb')->name('signin-web');
            Route::prefix('password/forgot')->name('password.forgot')->group(function () {
                Route::post('request', 'passwordForgotRequest')->name('request');
            });
        });
        // Authenticated
        Route::middleware(['auth:user'])->group(function () {
            Route::post('signout', 'signOut')->name('signout-web');
        });
    });
});

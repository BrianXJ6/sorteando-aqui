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
            Route::post('signin', 'signIn')->name('signin');
            Route::prefix('password/forgot')->name('password.forgot.')->group(function () {
                Route::post('request', 'passwordForgotRequest')->name('request');
                Route::post('reset', 'passwordForgotReset')->name('reset');
            });
        });
        // Authenticated
        Route::middleware(['auth:user'])->group(function () {
            Route::post('signout', 'signOut')->name('signout');
        });
    });
});

/*
// Register
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Password reset
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

// Verify
Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::post('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

// Password confirm
Route::get('password/confirm', 'Auth\ConfirmPasswordController@showConfirmForm')->name('password.confirm');
Route::post('password/confirm', 'Auth\ConfirmPasswordController@confirm');
*/

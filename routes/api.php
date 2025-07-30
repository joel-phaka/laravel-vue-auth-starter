<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController as PassportAccessTokenController;

Route::group([
    'prefix' => 'oauth'
], function () {
    Route::middleware('throttle')
        ->post('token', [PassportAccessTokenController::class, 'issueToken'])
        ->name('api.oauth.token');
    Route::post('token/refresh', [PassportAccessTokenController::class, 'issueToken'])
        ->name('api.oauth.token.refresh');
});

Route::group([
    'prefix' => 'auth'
], function () {
    Route::middleware(['verify.recaptcha'])
        ->group(function () {
            Route::post('login', [AuthController::class, 'login'])
                ->name('api.auth.login');
            Route::post('register', [RegisterController::class, 'register'])
                ->name('api.auth.register');
        });

    Route::group([
        'prefix' => 'password',
        'middleware' => ['throttle:6,1']
    ], function () {
        Route::post('email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
            ->name('api.auth.password.email');
        Route::post('reset', [ResetPasswordController::class, 'reset'])
            ->middleware(['verify.recaptcha'])
            ->name('api.auth.password.reset');
    });

    Route::post('token', [AuthController::class, 'issueToken'])
        ->name('api.auth.token');
    Route::post('token/refresh', [AuthController::class, 'refreshToken'])
        ->name('api.auth.token.refresh');

    Route::middleware(['auth.dynamic'])
        ->group(function () {
            Route::get('user', [AuthController::class, 'user'])
                ->name('api.auth.user');
            Route::post('logout', [AuthController::class, 'logout'])
                ->name('api.auth.logout');

            Route::group([
                'prefix' => 'verify',
                'middleware' => ['throttle:6,1']
            ], function () {
                Route::group([
                    'prefix' => 'email',
                ], function () {
                    Route::post('/', [EmailVerificationController::class, 'verifyEmail'])
                        ->name('api.auth.verify.email');
                    Route::post('resend', [EmailVerificationController::class, 'resendEmail'])
                        ->name('api.auth.verify.email.resend');
                });
            });
        });
});

Route::get('test', function () {

});

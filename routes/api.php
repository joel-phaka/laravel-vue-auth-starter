<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\OtpVerificationController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController as PassportAccessTokenController;

Route::group([
    'prefix' => 'oauth',
    'middleware' => ['feature:oauth']
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
    Route::group([
        'middleware' => ['recaptcha']
    ], function () {
        Route::post('login', [AuthController::class, 'login'])
            ->name('api.auth.login');
        Route::post('register', [RegisterController::class, 'register'])
            ->middleware(['feature:user_registration'])
            ->name('api.auth.register');
    });

    Route::group([
        'middleware' => ['feature:oauth,token_auth']
    ], function () {
        Route::post('token', [AuthController::class, 'issueToken'])
            ->name('api.auth.token');
        Route::post('token/refresh', [AuthController::class, 'refreshToken'])
            ->name('api.auth.token.refresh');
    });

    Route::group([
        'prefix' => 'password',
        'middleware' => ['feature:password_reset', 'throttle:6,1']
    ], function () {
        Route::post('email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
            ->name('api.auth.password.email');
        Route::post('reset', [ResetPasswordController::class, 'reset'])
            ->middleware(['recaptcha'])
            ->name('api.auth.password.reset');
    });

    Route::group([
        'middleware' => ['auth.dynamic']
    ], function () {
        Route::post('logout', [AuthController::class, 'logout'])
            ->name('api.auth.logout');

        Route::group([
            'middleware' => ['auth.active']
        ], function () {
            Route::group([
                'middleware' => ['auth.state']
            ], function () {
                Route::get('user', [AuthController::class, 'user'])
                    ->name('api.auth.user');
            });

            Route::group([
                'prefix' => 'verify',
                'middleware' => ['throttle:1,1']
            ], function () {
                Route::group([
                    'prefix' => 'otp',
                    'middleware' => ['feature:otp_verification']
                ], function () {
                    Route::post('/', [OtpVerificationController::class, 'verify'])
                        ->name('api.auth.verify.otp');
                    Route::post('resend', [OtpVerificationController::class, 'resend'])
                        ->name('api.auth.verify.otp.resend');
                });

                Route::group([
                    'prefix' => 'email',
                    'middleware' => ['feature:email_verification']
                ], function () {
                    Route::post('/', [EmailVerificationController::class, 'verify'])
                        ->name('api.auth.verify.email');
                    Route::post('resend', [EmailVerificationController::class, 'resend'])
                        ->name('api.auth.verify.email.resend');
                });
            });
        });
    });
});

Route::get('test', function () {

});

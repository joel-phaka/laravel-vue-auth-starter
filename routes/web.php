<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\Auth\SocialLoginController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'signin',
], function () {
    Route::group([
        'prefix' => 'oauth/{oauthProvider}',
        'middleware' => ['feature:oauth']
    ], function () {
        Route::get('/', [SocialLoginController::class, 'redirectToProvider'])
            ->name('auth.signin.provider');
        Route::get('callback', [SocialLoginController::class, 'handleProviderCallback'])
            ->name('auth.signin.provider.callback');
    });

});

Route::get('{any}', [AppController::class, 'index'])
    ->where('any', '.*')
    ->name('app.index');

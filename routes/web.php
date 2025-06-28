<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\Auth\SocialLoginController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'signin',
], function () {
    Route::get('/{provider}', [SocialLoginController::class, 'redirectToProvider'])
        ->whereIn('provider', config('auth.socialite.providers'))
        ->name('auth.signin.provider');
    Route::get('/{provider}/callback', [SocialLoginController::class, 'handleProviderCallback'])
        ->whereIn('provider', config('auth.socialite.providers'))
        ->name('auth.signin.provider.callback');
});

Route::get('{any}', [AppController::class, 'index'])
    ->where('any', '.*')
    ->name('app.index');

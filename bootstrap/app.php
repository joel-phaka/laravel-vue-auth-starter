<?php

use App\Exceptions\LoginException;
use App\Http\Middleware\DynamicAuth;
use App\Http\Middleware\VerifyAuthState;
use App\Http\Middleware\VerifyFeature;
use App\Http\Middleware\VerifyRecaptcha;
use App\Http\Middleware\VerifyUserRole;
use App\Http\Middleware\VerifyUserStatus;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(
            at: '*',
            headers:
                SymfonyRequest::HEADER_X_FORWARDED_FOR  |
                SymfonyRequest::HEADER_X_FORWARDED_HOST |
                SymfonyRequest::HEADER_X_FORWARDED_PORT |
                SymfonyRequest::HEADER_X_FORWARDED_PROTO
        );

        $middleware->alias([
            'auth.dynamic' => DynamicAuth::class,
            'auth.state' => VerifyAuthState::class,
            'auth.user_status' => VerifyUserStatus::class,
            'auth.role' => VerifyUserRole::class,
            'feature' => VerifyFeature::class,
            'recaptcha' => VerifyRecaptcha::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/oauth/*',
        ]);

        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle LoginException
        $exceptions->renderable(function (LoginException $e, Request $request) {
            return response()
                ->json($e->toArray())
                ->unauthorized();
        });

    })->create();

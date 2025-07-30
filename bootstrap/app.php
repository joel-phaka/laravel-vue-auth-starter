<?php

use App\Exceptions\AccessTokenException;
use App\Http\Middleware\DynamicAuth;
use App\Http\Middleware\VerifyActiveUser;
use App\Http\Middleware\VerifyRecaptcha;
use App\Http\Middleware\VerifyUserRole;
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
            'verify.role' => VerifyUserRole::class,
            'verify.active' => VerifyActiveUser::class,
            'verify.recaptcha' => VerifyRecaptcha::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/oauth/*',
        ]);

        $middleware->statefulApi();
        $middleware->append(VerifyActiveUser::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle only AccessTokenException
        $exceptions->renderable(function (AccessTokenException $e, Request $request) {
            return response()
                ->json($e->toArray())
                ->unauthorized();
        });
    })->create();

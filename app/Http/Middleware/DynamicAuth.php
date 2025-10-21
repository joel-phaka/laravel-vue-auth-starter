<?php

namespace App\Http\Middleware;

use App\Enums\AuthState;
use App\Enums\UserStatus;
use App\Exceptions\AuthStateException;
use App\Exceptions\LoginException;
use App\Support\Auth\AuthUtils;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DynamicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @throws LoginException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->is('api/*')) return $next($request);

        // Use JWT if the Authorization header starts with 'Bearer'
        $authHeader = strval($request->header('Authorization'));

        if (str_starts_with($authHeader, 'Bearer ')) {
            Auth::shouldUse('api');
        } else {
            Auth::shouldUse('sanctum');
        }

        if (!Auth::check()) {
            throw new LoginException();
        }

        return $next($request);
    }
}

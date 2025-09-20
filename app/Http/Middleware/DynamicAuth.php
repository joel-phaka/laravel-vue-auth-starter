<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DynamicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->is('api/*')) return $next($request);

        // Use JWT if the Authorization header starts with 'Bearer'
        $authHeader = strval($request->header('Authorization'));

        if (str_starts_with($authHeader, 'Bearer ')) {
            Auth::shouldUse('api');
            config(['auth.auth_type' => 'token']);
        } else {
            Auth::shouldUse('sanctum');
            config(['auth.auth_type' => 'session']);
        }

        if (!Auth::check()) {
            return response()
                ->json(['message' => 'Unauthorized'])
                ->unauthorized();
        }

        return $next($request);
    }
}

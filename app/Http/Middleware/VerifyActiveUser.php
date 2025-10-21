<?php

namespace App\Http\Middleware;

use App\Exceptions\LoginException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @throws LoginException
     */
    public function handle(Request $request, Closure $next): Response
    {
        return (new VerifyUserStatus)->handle($request, $next, 'active');
    }
}

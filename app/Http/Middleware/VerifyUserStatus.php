<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use App\Exceptions\LoginException;
use App\Exceptions\UserStatusException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @throws LoginException
     */
    public function handle(Request $request, Closure $next, string $status): Response
    {
        if (!Auth::check()) {
            throw new LoginException();
        }

        $expectedUserStatus = UserStatus::tryFrom($status);

        if ($expectedUserStatus != Auth::user()->status) {
            throw new UserStatusException(Auth::user()->status);
        }

        return $next($request);
    }
}

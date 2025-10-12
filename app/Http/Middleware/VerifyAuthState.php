<?php

namespace App\Http\Middleware;

use App\Enums\AuthState;
use App\Exceptions\AuthStateException;
use App\Exceptions\LoginException;
use App\Support\Auth\AuthUtils;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyAuthState
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @throws LoginException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            throw new LoginException();
        }

        $currentLogin = AuthUtils::getCurrentLogin();

        if ($currentLogin->auth_state == AuthState::LOGGED_IN) {
            return $next($request);
        }

        if (in_array($currentLogin->auth_state, [
            AuthState::PENDING_LOGIN_VERIFICATION,
            AuthState::PENDING_REGISTRATION_VERIFICATION
        ])) {
            throw new AuthStateException($currentLogin->auth_state);
        }

        throw new LoginException();
    }
}

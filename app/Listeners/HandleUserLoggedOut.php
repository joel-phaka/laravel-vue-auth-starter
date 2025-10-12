<?php

namespace App\Listeners;

use App\Enums\AuthState;
use App\Enums\AuthType;
use App\Events\UserLoggedOut;
use App\Support\Auth\AuthUtils;
use Illuminate\Support\Facades\Auth;

class HandleUserLoggedOut
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserLoggedOut $event): void
    {
        $currentLogin = AuthUtils::getCurrentLogin();
        $currentLogin?->update(['auth_state' => AuthState::LOGGED_OUT]);

        if ($currentLogin?->auth_type == AuthType::SESSION) {
            Auth::guard('web')->logout();
            session()->invalidate();
            session()->regenerateToken();
        } else if ($currentLogin?->auth_type == AuthType::ACCESS_TOKEN) {
            auth()->user()->token()->revoke();
        }
    }
}

<?php

namespace App\Listeners;

use App\Enums\AuthState;
use App\Events\UserLoggedIn;
use App\Support\Auth\AuthEventData;
use App\Support\Feature;
use App\Traits\CreatesUserLogin;
use Illuminate\Support\Facades\Auth;


class HandleUserLoggedIn
{
    use CreatesUserLogin;

    public AuthEventData $authEventData;

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
    public function handle(UserLoggedIn $event): void
    {
        $this->authEventData = $event->getAuthEventData();
        $user = $event->getAuthEventData()->getUser();

        $isOtpVerificationEnabled = Feature::isEnabled('otp_verification');
        $authState = $isOtpVerificationEnabled
            ? AuthState::PENDING_LOGIN_VERIFICATION
            : AuthState::LOGGED_IN;


        $this->createUserLogin($authState, $event->getAuthEventData());
    }
}

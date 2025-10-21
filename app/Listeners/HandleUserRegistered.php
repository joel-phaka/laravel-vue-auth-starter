<?php

namespace App\Listeners;

use App\Enums\AuthState;
use App\Events\UserRegistered;
use App\Models\User;
use App\Support\Auth\AuthEventData;
use App\Support\Feature;
use App\Traits\CreatesUserLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HandleUserRegistered
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
     * @throws \Throwable
     */
    public function handle(UserRegistered $event): void
    {
        $this->authEventData = $event->getAuthEventData();
        $user = $event->getAuthEventData()->getUser();

        $isEmailVerificationEnabled = Feature::isEnabled('email_verification');
        $authState = $isEmailVerificationEnabled
            ? AuthState::PENDING_REGISTRATION_VERIFICATION
            : AuthState::LOGGED_IN;

        $this->createUserLogin($authState, $event->getAuthEventData());

        if ($isEmailVerificationEnabled) {
            // Send email verification
            $this->sendEmailVerification($user);
        }

        // Log the registration
        $this->logRegistration($user);

        // $this->sendWelcomeEmail($user);
        // $this->assignDefaultPermissions($user);
    }

    /**
     * Send email verification to the user
     */
    private function sendEmailVerification(User $user): void
    {
        $user->sendEmailVerificationNotification();
    }

    /**
     * Log the user registration
     */
    private function logRegistration($user): void
    {
        Log::info('User registered successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->first_name . ' ' . $user->last_name,
            'registered_at' => now()
        ]);
    }

    /**
     * Send a welcome email to the user
     */
    private function sendWelcomeEmail($user): void
    {
        // Implementation for welcome email
        // You can use Laravel's notification system here
    }

    /**
     * Assign default permissions or roles
     */
    private function assignDefaultPermissions($user): void
    {
        // Implementation for assigning default permissions
        // This could be role-based permissions, feature flags, etc.
    }
}

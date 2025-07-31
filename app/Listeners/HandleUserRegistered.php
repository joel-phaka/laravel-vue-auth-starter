<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Illuminate\Support\Facades\Log;

class HandleUserRegistered
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
    public function handle(UserRegistered $event): void
    {
        $user = $event->getUser();

        // Send email verification
        $this->sendEmailVerification($user);

        // Log the registration
        $this->logRegistration($user);

        // $this->sendWelcomeEmail($user);
        // $this->assignDefaultPermissions($user);
    }

    /**
     * Send email verification to the user
     */
    private function sendEmailVerification($user): void
    {
        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            Log::error('Failed to send email verification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }
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

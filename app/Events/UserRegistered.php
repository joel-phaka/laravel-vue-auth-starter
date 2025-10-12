<?php

namespace App\Events;

use App\Models\User;
use App\Support\Auth\AuthEventData;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegistered
{
    use Dispatchable, SerializesModels;

    private AuthEventData $authEventData;

    /**
     * Create a new event instance.
     */
    public function __construct(AuthEventData $authEventData)
    {
        $this->authEventData = $authEventData;
    }

    /**
     * @return AuthEventData
     */
    public function getAuthEventData(): AuthEventData
    {
        return $this->authEventData;
    }
}

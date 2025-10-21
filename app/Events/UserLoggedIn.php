<?php

namespace App\Events;

use App\Support\Auth\AuthEventData;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoggedIn
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

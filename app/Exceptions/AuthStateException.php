<?php

namespace App\Exceptions;

use App\Enums\AuthState;
use App\Exceptions\LoginException;
use Illuminate\Support\Facades\Auth;

class AuthStateException extends LoginException
{
    private AuthState $authState;

    public function __construct(AuthState $authState)
    {
        parent::__construct(reason: $authState->value);
        $this->authState = $authState;
    }

    public function getAuthState(): AuthState
    {
        return $this->authState;
    }
}

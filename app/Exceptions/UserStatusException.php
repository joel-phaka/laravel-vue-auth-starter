<?php

namespace App\Exceptions;

use App\Enums\UserStatus;
use App\Exceptions\LoginException;

class UserStatusException extends LoginException
{
    private UserStatus $userStatus;

    public function __construct(UserStatus $userStatus)
    {
        parent::__construct(reason: $userStatus->value);
        $this->userStatus = $userStatus;
    }

    public function getStatus(): UserStatus
    {
        return $this->userStatus;
    }
}

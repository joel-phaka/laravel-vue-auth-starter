<?php

namespace App\Support;

use App\Enums\AuthType;
use App\Models\User;

class UserLoginInfo
{
    public User $user;
    public AuthType $authType;
    public string|int $authTypeId;

    public function __construct(User $user, AuthType $authType, string|int $authTypeId)
    {
        $this->user = $user;
        $this->authType = $authType;
        $this->authTypeId = $authTypeId;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return AuthType
     */
    public function getAuthType(): AuthType
    {
        return $this->authType;
    }

    /**
     * @return int|string
     */
    public function getAuthTypeId(): int|string
    {
        return $this->authTypeId;
    }
}

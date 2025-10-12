<?php

namespace App\Support\Auth;

use App\Enums\AuthType;
use App\Models\User;
use PeterPetrus\Auth\PassportToken;

class AuthEventData
{
    private User $user;
    private AuthType $authType;
    private string|int $authTypeId;
    private ?int $oauthProviderId;
    private array $extraData;

    public function __construct(User $user, AuthType $authType, string|int $authTypeId, ?int $oauthProviderId = null, array $extraData = [])
    {
        $this->user = $user;
        $this->authType = $authType;
        $this->authTypeId = $authTypeId;
        $this->oauthProviderId = $oauthProviderId;
        $this->extraData = $extraData;
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

    /**
     * @return int|null
     */
    public function getOauthProviderId(): ?int
    {
        return $this->oauthProviderId;
    }

    public function getExtraData(): array
    {
        return $this->extraData;
    }
}

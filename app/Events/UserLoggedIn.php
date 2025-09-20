<?php

namespace App\Events;

use App\Models\User;
use App\Support\UserLoginInfo;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoggedIn
{
    use Dispatchable, SerializesModels;

    private UserLoginInfo $userLoginInfo;

    /**
     * Create a new event instance.
     */
    public function __construct(UserLoginInfo $userLoginInfo)
    {
        $this->userLoginInfo = $userLoginInfo;
    }

    /**
     * @return UserLoginInfo
     */
    public function getUserLoginInfo(): UserLoginInfo
    {
        return $this->userLoginInfo;
    }
}

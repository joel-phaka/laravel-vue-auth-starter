<?php

namespace App\Enums;

enum UserStatus : string
{
    case ACTIVE = 'active';
    case INACTIVE ='inactive';
    case DEACTIVATED = 'deactivated';
    case SUSPENDED = 'suspended';
    case BANNED = 'banned';
    case LOCKED = 'locked';
}

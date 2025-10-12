<?php

namespace App\Enums;

enum AuthState: string
{
    case PENDING_REGISTRATION_VERIFICATION = 'pending_registration_verification';
    case PENDING_LOGIN_VERIFICATION = 'pending_login_verification';
    case LOGGED_IN = 'logged_in';
    case LOGGED_OUT = 'logged_out';
    case BLOCKED = 'blocked';
}

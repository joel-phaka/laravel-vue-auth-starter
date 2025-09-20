<?php

namespace App\Enums;

enum AuthType: string
{
    case SESSION = 'session';
    case ACCESS_TOKEN = 'access_token';
}

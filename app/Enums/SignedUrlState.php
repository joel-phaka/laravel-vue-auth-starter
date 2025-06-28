<?php

namespace App\Enums;

enum SignedUrlState : string
{
    case INVALID_URL = "invalid_url";
    case EXPIRED_URL = "expired_url";
    case VALID_URL = "valid_url";
}

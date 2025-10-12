<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Arrayable;

class AccessTokenException extends LoginException
{
    private array $data;

    public function __construct(?array $data = null, ?string $reason = null)
    {
        parent::__construct();

        $this->data = $data ?? [];
        $error = $this->data['error'] ?? null;
        $error_description = $this->data['error_description'] ?? null;

        if (!empty($error_description)) {
            if (str_contains($error_description, 'refresh token') && str_contains($error_description, 'invalid')) {
                $this->setReason('invalid_refresh_token');
            } else if (str_contains($error_description, 'credentials') && str_contains($error_description, 'invalid')) {
                $this->setReason('invalid_credentials');
            }
        } else if (!empty($error) && str_contains($error, 'invalid')) {
            $this->setReason('invalid_credentials');
        }

        if (!!$this->getReason()) {
            $this->setReason($reason);
        }
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}

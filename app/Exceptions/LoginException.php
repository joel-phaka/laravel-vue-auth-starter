<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Arrayable;

class LoginException extends AuthenticationException implements Arrayable
{
    private string $reason;

    public function __construct(string $message = 'Unauthorized', string $reason = 'unauthorized')
    {
        parent::__construct($message);
        $this->code = 401;
        $this->redirectTo = '/signin';
        $this->setReason($reason);
    }

    protected function setReason(string $reason): void
    {
        $this->reason = 'auth_'. strtolower(preg_replace('/auth_/i', '', trim($reason)) ?: 'unauthorized');
    }
    public function getReason(): string
    {
        return $this->reason;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'reason' => $this->reason
        ];
    }
}

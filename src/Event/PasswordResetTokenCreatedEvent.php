<?php

namespace App\Event;

use App\Entity\PasswordResetToken;
use App\Entity\User;

final class PasswordResetTokenCreatedEvent
{
    private PasswordResetToken $token;

    public function __construct(PasswordResetToken $token)
    {
        $this->token = $token;
    }

    public function getUser(): User
    {
        return $this->token->getUser();
    }

    public function getToken(): PasswordResetToken
    {
        return $this->token;
    }
}

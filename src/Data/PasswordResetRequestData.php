<?php

namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

final class PasswordResetRequestData
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public $email = '';

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): PasswordResetRequestData
    {
        $this->email = $email;

        return $this;
    }
}

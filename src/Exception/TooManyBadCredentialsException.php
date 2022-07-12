<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Throwable;

class TooManyBadCredentialsException extends CustomUserMessageAuthenticationException
{
    /**
     * TooManyBadCredentialsException constructor.
     * @param string $message
     * @param array $messageData
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message = 'Le compte a été verrouillé suite à de trop nombreuses tentatives de connexion',
        array $messageData = [],
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $messageData, $code, $previous);
    }
}

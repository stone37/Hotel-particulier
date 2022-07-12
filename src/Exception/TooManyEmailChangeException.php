<?php

namespace App\Exception;

use App\Entity\EmailVerification;
use Exception;

class TooManyEmailChangeException extends Exception
{
    public EmailVerification $emailVerification;

    public function __construct(EmailVerification $emailVerification)
    {
        parent::__construct();
        $this->emailVerification = $emailVerification;
    }
}

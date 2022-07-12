<?php

namespace App\Event;

use App\Entity\EmailVerification;

class EmailVerificationEvent
{
    public EmailVerification $emailVerification;

    public function __construct(EmailVerification $emailVerification)
    {
        $this->emailVerification = $emailVerification;
    }
}

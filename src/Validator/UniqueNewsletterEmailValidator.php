<?php

namespace App\Validator;

use App\Entity\NewsletterData;
use App\Repository\NewsletterDataRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueNewsletterEmailValidator extends ConstraintValidator
{
    private NewsletterDataRepository $repository;

    public function __construct(NewsletterDataRepository $repository)
    {
        $this->repository = $repository;
    }

    public function validate($email, Constraint $constraint)
    {
        if ($this->isEmailValid($email) === false) {
            $this->context->addViolation($constraint->message);
        }
    }

    private function isEmailValid($email)
    {
        $newsletter = $this->repository->findOneBy(['email' => $email]);

        if ($newsletter instanceof NewsletterData) {
            return false;
        }

        return true;
    }
}


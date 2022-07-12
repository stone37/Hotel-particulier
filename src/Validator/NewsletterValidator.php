<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NewsletterValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate($email)
    {
        $violations = $this->validator->validate($email, [
            new Email(['message' => 'L\'adresse email est invalide']),
            new NotBlank(['message' => 'Veuillez remplir le champ d\'email']),
            new UniqueNewsletterEmail(),
        ]);


        $errors = [];

        if (count($violations) === 0) {
            return $errors;
        }

        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $errors[] = $violation->getMessage();
        }

        return $errors;
    }
}


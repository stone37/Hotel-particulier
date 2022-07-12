<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class UniqueNewsletterEmail extends Constraint
{
    public $message = 'Vous êtes déjà inscrit sur notre newsletter.';

    public function validatedBy()
    {
        return static::class.'Validator';
    }
}


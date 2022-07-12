<?php

namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class ContactData
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    public $name;

    #[Assert\NotBlank]
    #[Assert\Email]
    public $email;

    #[Assert\NotBlank]
    public $phone;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    public $content;
}


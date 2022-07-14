<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MenuController extends AbstractController
{
    use ControllerTrait;

    public function dropdownMenu()
    {
        if ($this->getUser())
            $user = $this->getUserOrThrow();
        else
            $user = null;

        return $this->render('site/menu/dropdown.html.twig', [
            'user' => $user,
        ]);
    }
}


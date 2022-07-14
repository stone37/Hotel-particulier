<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    #[Route(path: '/a-propos', name: 'app_env')]
    public function env(): Response
    {
        return $this->render('site/pages/env.html.twig');
    }

    #[Route(path: '/politique-de-confidentialite', name: 'app_confidentialite')]
    public function confidentialite(): Response
    {
        return $this->render('site/pages/confidentialite.html.twig');
    }

    #[Route(path: '/condition-d-utilisation', name: 'app_cgu')]
    public function cgu(): Response
    {
        return $this->render('site/pages/cgu.html.twig');
    }

    #[Route(path: '/condition-de-reservation', name: 'app_cgr')]
    public function bookingCondition(): Response
    {
        return $this->render('site/pages/bookingCondition.html.twig');
    }
}




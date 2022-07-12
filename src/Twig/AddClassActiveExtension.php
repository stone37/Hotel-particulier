<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AddClassActiveExtension extends AbstractExtension
{
    private RequestStack $request;

    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('isActive', array($this, 'check'))
        );
    }

    public function check($routesToCheck)
    {
        $currentRoute = $this->request->getMainRequest()->get('_route');
        
        if ($routesToCheck == $currentRoute) {
            return true;
        }

        return false;
    }
}
<?php

namespace App\Twig;

use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class BreadcrumbExtension extends AbstractExtension
{
    private Breadcrumbs $breadcrumbs;
    private RouterInterface $router;

    public function __construct(Breadcrumbs $breadcrumbs, RouterInterface $router)
    {
        $this->breadcrumbs = $breadcrumbs;
        $this->router = $router;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('breadcrumb', array($this, 'addBreadcrumb'))
        );
    }

    public function addBreadcrumb($label, $url = '', array $translationParameters = array())
    {
        if (!$this->breadcrumbs->count()) {
            $this->breadcrumbs->addItem('Accueil', $this->router->generate('app_home'));
        }

        $this->breadcrumbs->addItem($label, $url, $translationParameters);
    }
}
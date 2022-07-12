<?php

namespace App\Twig;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Router;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class BreadcrumbExtension extends AbstractExtension
{
    /**
     * @var Breadcrumbs
     */
    private $breadcrumbs;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param Breadcrumbs $breadcrumbs
     * @param Router $router
     */
    public function __construct(Breadcrumbs $breadcrumbs, RouterInterface $router)
    {
        $this->breadcrumbs = $breadcrumbs;
        $this->router = $router;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('breadcrumb', array($this, 'addBreadcrumb'))
        );
    }

    /**
     * @param $label
     * @param string $url
     * @param array $translationParameters
     */
    public function addBreadcrumb($label, $url = '', array $translationParameters = array())
    {
        if (!$this->breadcrumbs->count()) {
            $this->breadcrumbs->addItem('Accueil', $this->router->generate('app_home'));
        }

        $this->breadcrumbs->addItem($label, $url, $translationParameters);
    }
}
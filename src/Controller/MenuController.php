<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Settings;
use App\Manager\SettingsManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MenuController extends AbstractController
{
    use ControllerTrait;

    private ?Settings $settings;

    public function __construct(SettingsManager $manager)
    {
        $this->settings = $manager->get();
    }

    public function dropdownMenu()
    {
        if ($this->getUser())
            $user = $this->getUserOrThrow();
        else
            $user = null;

        return $this->render('site/menu/dropdown.html.twig', [
            'settings' => $this->settings,
            'user' => $user,
        ]);
    }
}


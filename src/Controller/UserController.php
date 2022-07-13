<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Settings;
use App\Manager\SettingsManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    use ControllerTrait;

    private ?Settings $settings;

    public function __construct(SettingsManager $manager)
    {
        //$this->settings = $manager->get();
    }


}


<?php

namespace App\Twig;

use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PrefixUserNameExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return array(
            new TwigFunction('app_prefix_user_name', array($this, 'prefix'))
        );
    }

    public function prefix(User $user)
    {
        $name = explode(" ", $user->getFirstName());
        $prefix = substr($name[count($name)-1],0,1);

        return strtoupper($prefix);
    }
}
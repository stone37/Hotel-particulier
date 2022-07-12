<?php

namespace App\Controller\Traits;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

Trait ControllerTrait
{
    private function breadcrumb(Breadcrumbs $breadcrumbs)
    {
        $breadcrumbs->addItem('Acceuil', $this->generateUrl('app_home'));

        return $breadcrumbs;
    }

    private function getUserOrThrow(): User
    {
        $user = $this->getUser();

        if (!($user instanceof User)) {
            throw new AccessDeniedException();
        }

        return $user;
    }
}


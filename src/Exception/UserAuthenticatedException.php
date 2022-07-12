<?php

namespace App\Exception;

use App\Entity\User;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * L'utilisateur est déjà authentifié.
 */
class UserAuthenticatedException extends AuthenticationException
{
    private $user;
    private $resourceOwner;

    public function __construct(User $user, ResourceOwnerInterface $resourceOwner)
    {
        parent::__construct([]);

        $this->user = $user;
        $this->resourceOwner = $resourceOwner;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getResourceOwner(): ResourceOwnerInterface
    {
        return $this->resourceOwner;
    }
}


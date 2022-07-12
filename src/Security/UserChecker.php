<?php

namespace App\Security;

use App\Exception\TooManyBadCredentialsException;
use App\Exception\UserBannedException;
use App\Exception\UserNotFoundException;
use App\Service\LoginAttemptService;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Bloque l'authentification de l'utilisateur.
 */
class UserChecker implements UserCheckerInterface
{
    private $loginAttemptService;

    public function __construct(LoginAttemptService $loginAttemptService)
    {
        $this->loginAttemptService = $loginAttemptService;
    }

    /**
     * Vérifie que l'utilisateur a le droit de se connecter.
     *
     * @param UserInterface $user
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if ($user instanceof User && $this->loginAttemptService->limitReachedFor($user)) {
            throw new TooManyBadCredentialsException();
        }

        return;
    }

    /**
     * Vérifie que l'utilisateur connecté a le droit de continuer.
     *
     * @param UserInterface $user
     */
    public function checkPostAuth(UserInterface $user): void
    {
        if ($user instanceof User && $user->isBanned()) {
            throw new UserBannedException();
        }

        if ($user instanceof User && null !== $user->getConfirmationToken()) {
            throw new UserNotFoundException();
        }

        return;
    }
}

<?php

namespace App\Authenticator;

use App\Exception\NotVerifiedEmailException;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class GoogleAuthenticator extends AbstractSocialAuthenticator
{
    protected string $serviceName = 'google';

    public function authenticate(Request $request): Passport
    {
        $client = $this->getClient();
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {

                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);

                if (true !== ($googleUser->toArray()['email_verified'] ?? null)) {
                    throw new NotVerifiedEmailException();
                }

                $user = $this->repository->findForOauth('google', $googleUser->getId(), $googleUser->getEmail());

                if ($user && null === $user->getGoogleId()) {
                    $user->setGoogleId($googleUser->getId());
                    $this->em->flush();
                }

                return $user;
            })
        );
    }
}

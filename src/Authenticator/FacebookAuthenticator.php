<?php

namespace App\Authenticator;

use App\Exception\EmailAlreadyUsedException;
use League\OAuth2\Client\Provider\FacebookUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class FacebookAuthenticator extends AbstractSocialAuthenticator
{
    protected string $serviceName = 'facebook';

    public function authenticate(Request $request): Passport
    {
        $client = $this->getClient();
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {

                /** @var FacebookUser $facebookUser */
                $facebookUser = $client->fetchUserFromToken($accessToken);

                $user = $this->repository->findForOauth('facebook', $facebookUser->getId(), $facebookUser->getEmail());

                if ($user && null === $user->getFacebookId()) {
                    throw new EmailAlreadyUsedException();
                }

                return $user;
            })
        );
    }
}

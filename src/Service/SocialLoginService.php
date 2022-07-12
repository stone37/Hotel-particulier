<?php

namespace App\Service;

use App\Entity\User;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SocialLoginService
{
    public const SESSION_KEY = 'oauth_login';

    private RequestStack $request;
    private NormalizerInterface $normalizer;

    public function __construct(RequestStack $request, NormalizerInterface $normalizer)
    {
        $this->request = $request;
        $this->normalizer = $normalizer;
    }

    /**
     * @param ResourceOwnerInterface $resourceOwner
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function persist(ResourceOwnerInterface $resourceOwner): void
    {
        $data = $this->normalizer->normalize($resourceOwner);
        $this->request->getSession()->set(self::SESSION_KEY, $data);
    }

    public function hydrate(User $user): bool
    {
        $oauthData = $this->request->getSession()->get(self::SESSION_KEY);

        if (null === $oauthData || !isset($oauthData['email'])) {
            return false;
        }

        $user->setEmail($oauthData['email']);
        $user->setGoogleId($oauthData['google_id'] ?? null);
        $user->setFacebookId($oauthData['facebook_id'] ?? null);
        $user->setUsername($oauthData['username']);
        $user->setConfirmationToken(null);

        return true;
    }

    public function getOauthType(): ?string
    {
        $oauthData = $this->request->getSession()->get(self::SESSION_KEY);

        return $oauthData ? $oauthData['type'] : null;
    }
}





<?php

namespace App\Normalizer;

use League\OAuth2\Client\Provider\FacebookUser;

class FacebookNormalizer extends Normalizer
{
    /**
     * @param FacebookUser $object
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        return [
            'email' => $object->getEmail(),
            'facebook_id' => $object->getId(),
            'type' => 'Facebook',
            'username' => $object->getName(),
            'firstName' => $object->getFirstName(),
            'lastName' => $object->getLastName(),
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof FacebookUser;
    }
}

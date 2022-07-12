<?php

namespace App\Normalizer;

use League\OAuth2\Client\Provider\GoogleUser;

class GoogleNormalizer extends Normalizer
{
    /**
     * @param GoogleUser $object
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        return [
            'email' => $object->getEmail(),
            'google_id' => $object->getId(),
            'type' => 'Google',
            'username' => $object->getName(),
            'firstName' => $object->getFirstName(),
            'lastName' => $object->getLastName(),
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof GoogleUser;
    }
}

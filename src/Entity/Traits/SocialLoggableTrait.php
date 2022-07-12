<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SocialLoggableTrait
{
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $googleId = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $facebookId = null;

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): self
    {
        $this->googleId = $googleId;

        return $this;
    }

    public function getFacebookId(): ?string
    {
        return $this->facebookId;
    }

    public function setFacebookId(?string $facebookId): self
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    public function useOauth(): bool
    {
        return null !== $this->googleId || null !== $this->facebookId;
    }
}


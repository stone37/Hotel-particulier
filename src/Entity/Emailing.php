<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\EmailingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailingRepository::class)]
class Emailing
{
    const GROUP_PARTICULIER = 'particulier';
    const GROUP_USER = 'user';
    const GROUP_NEWSLETTER = 'newsletter';

    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $destinataire = '';

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $subject = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $content  = '';

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $groupe = self::GROUP_PARTICULIER;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDestinataire(): ?string
    {
        return $this->destinataire;
    }

    public function setDestinataire(?string $destinataire): self
    {
        $this->destinataire = $destinataire;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getGroupe(): ?string
    {
        return $this->groupe;
    }

    public function setGroupe(string $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }
}

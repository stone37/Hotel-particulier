<?php

namespace App\Dto;

use App\Entity\User;
use App\Validator\Unique;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Données pour la mise à jour du profil utilisateur.
 *
 * @Unique(entityClass="App\Entity\User", field="email")
 * @Unique(entityClass="App\Entity\User", field="phone")
 * @Unique(entityClass="App\Entity\User", field="username")
 */
class ProfileUpdateDto
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 100)]
    #[Assert\Email]
    public ?string $email;

    #[Assert\NotBlank]
    public ?string $username = '';

    #[Assert\NotBlank(normalizer: 'trim', message: 'Entrez un prénom s\'il vous plait.')]
    #[Assert\Length(min: 2, max: 180, minMessage: 'Le prénom est trop court.', maxMessage: 'Le prénom est trop long.')]
    public ?string $firstname;

    #[Assert\NotBlank(message: 'Entrez un prénom s\'il vous plait.')]
    #[Assert\Length(min: 2, max: 180, minMessage: 'Le prénom est trop court.', maxMessage: 'Le prénom est trop long.')]
    public ?string $lastname;

    #[Assert\NotBlank(message: 'Entrez un numéro de téléphone s\'il vous plait.')]
    #[Assert\Length(min: 10, max: 180, minMessage: 'Le numéro de téléphone est trop court.', maxMessage: 'Le numéro de téléphone est trop long.')]
    public ?string $phone;

    public ?string $address;

    public ?string $country;

    public ?string $city;

    public $user;

    public function __construct(User $user)
    {
        $this->email = $user->getEmail();
        $this->username = $user->getUsername();
        $this->firstname = $user->getFirstname();
        $this->lastname = $user->getLastname();
        $this->phone = $user->getPhone();
        $this->city = $user->getCity();
        $this->country = $user->getCountry();
        $this->user = $user;
    }

    public function getId(): int
    {
        return $this->user->getId() ?: 0;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username ?: '';

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }
}

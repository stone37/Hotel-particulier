<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\DiscountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscountRepository::class)]
class Discount
{
    const FIXED_PRICE = 'fixed';
    const PERCENT_REDUCTION = 'percent';

    use EnabledTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $discount = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $code = '';

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $type = Discount::PERCENT_REDUCTION;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $utilisation = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $utiliser = null;

    #[ORM\OneToMany(mappedBy: 'discount', targetEntity: Commande::class)]
    private $commandes = null;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(int $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUtilisation(): ?int
    {
        return $this->utilisation;
    }

    public function setUtilisation(?int $utilisation): self
    {
        $this->utilisation = $utilisation;

        return $this;
    }

    public function getUtiliser(): ?int
    {
        return $this->utiliser;
    }

    public function setUtiliser(?int $utiliser): self
    {
        $this->utiliser = $utiliser;

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): ?Collection
    {
        return $this->commandes;
    }

    public function addCommande(?Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setDiscount($this);
        }

        return $this;
    }

    public function removeCommande(?Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getDiscount() === $this) {
                $commande->setDiscount(null);
            }
        }

        return $this;
    }
}

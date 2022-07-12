<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    use TimestampableTrait;
    use PositionTrait;
    use EnabledTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Gedmo\Slug(fields: ['name'], unique: true)]
    private ?string $slug;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $smoker = null;

    #[ORM\Column(type: 'integer', length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?int $roomNumber = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\NotBlank]
    private ?int $price = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\NotBlank]
    private ?int $maximumAdults = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\NotBlank]
    private ?int $maximumOfChildren = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $area = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description  = '';

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $type = 'simple';

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $couchage = '';

    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool $taxeStatus = false;

    #[ORM\OneToMany(mappedBy: 'room', targetEntity: Promotion::class, orphanRemoval: true)]
    private ArrayCollection $promotions;

    #[ORM\ManyToOne(targetEntity: Taxe::class, inversedBy: 'rooms')]
    private ?Taxe $taxe;

    #[ORM\ManyToMany(targetEntity: Supplement::class, mappedBy: 'rooms')]
    private ArrayCollection $supplements;

    #[ORM\ManyToMany(targetEntity: Option::class, mappedBy: 'rooms')]
    private ArrayCollection $options;

    #[ORM\ManyToMany(targetEntity: RoomEquipment::class, mappedBy: 'rooms', cascade: ['persist'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $equipments;

    #[ORM\OneToMany(mappedBy: 'room', targetEntity: RoomGallery::class, cascade: ['ALL'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private ArrayCollection $galleries;

    #[ORM\OneToMany(mappedBy: 'room', targetEntity: Booking::class)]
    private $bookings;

    public function __construct()
    {
        $this->promotions = new ArrayCollection();
        $this->supplements = new ArrayCollection();
        $this->options = new ArrayCollection();
        $this->requipments = new ArrayCollection();
        $this->galleries = new ArrayCollection();
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSmoker(): ?string
    {
        return $this->smoker;
    }

    public function setSmoker(?string $smoker): self
    {
        $this->smoker = $smoker;

        return $this;
    }

    public function getRoomNumber(): ?int
    {
        return $this->roomNumber;
    }

    public function setRoomNumber(?int $roomNumber): self
    {
        $this->roomNumber = $roomNumber;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getMaximumAdults(): ?int
    {
        return $this->maximumAdults;
    }

    public function setMaximumAdults(?int $maximumAdults): self
    {
        $this->maximumAdults = $maximumAdults;

        return $this;
    }

    public function getMaximumOfChildren(): ?int
    {
        return $this->maximumOfChildren;
    }

    public function setMaximumOfChildren(?int $maximumOfChildren): self
    {
        $this->maximumOfChildren = $maximumOfChildren;

        return $this;
    }

    public function getArea(): ?int
    {
        return $this->area;
    }

    public function setArea(?int $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCouchage(): ?string
    {
        return $this->couchage;
    }

    public function setCouchage(?string $couchage): self
    {
        $this->couchage = $couchage;

        return $this;
    }

    public function isTaxeStatus(): ?bool
    {
        return $this->taxeStatus;
    }

    public function setTaxeStatus(bool $taxeStatus): self
    {
        $this->taxeStatus = $taxeStatus;

        return $this;
    }

    /**
     * @return Collection<int, Promotion>
     */
    public function getPromotions(): Collection
    {
        return $this->promotions;
    }

    public function addPromotion(Promotion $promotion): self
    {
        if (!$this->promotions->contains($promotion)) {
            $this->promotions[] = $promotion;
            $promotion->setRoom($this);
        }

        return $this;
    }

    public function removePromotion(Promotion $promotion): self
    {
        if ($this->promotions->removeElement($promotion)) {
            // set the owning side to null (unless already changed)
            if ($promotion->getRoom() === $this) {
                $promotion->setRoom(null);
            }
        }

        return $this;
    }

    public function getTaxe(): ?Taxe
    {
        return $this->taxe;
    }

    public function setTaxe(?Taxe $taxe): self
    {
        $this->taxe = $taxe;

        return $this;
    }

    /**
     * @return Collection<int, Supplement>
     */
    public function getSupplements(): Collection
    {
        return $this->supplements;
    }

    public function addSupplement(Supplement $supplement): self
    {
        if (!$this->supplements->contains($supplement)) {
            $this->supplements[] = $supplement;
            $supplement->addRoom($this);
        }

        return $this;
    }

    public function removeSupplement(Supplement $supplement): self
    {
        if ($this->supplements->removeElement($supplement)) {
            $supplement->removeRoom($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Option>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
            $option->addRoom($this);
        }

        return $this;
    }

    public function removeOption(Option $option): self
    {
        if ($this->options->removeElement($option)) {
            $option->removeRoom($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, RoomEquipment>
     */
    public function getEquipments(): Collection
    {
        return $this->equipments;
    }

    public function addEquipment(RoomEquipment $equipment): self
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments[] = $equipment;
            $equipment->addRoom($this);
        }

        return $this;
    }

    public function removeEquipment(RoomEquipment $equipment): self
    {
        if ($this->equipments->removeElement($equipment)) {
            $equipment->removeRoom($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, RoomGallery>
     */
    public function getGalleries(): Collection
    {
        return $this->galleries;
    }

    public function addGallery(RoomGallery $gallery): self
    {
        if (!$this->galleries->contains($gallery)) {
            $this->galleries[] = $gallery;
            $gallery->setRoom($this);
        }

        return $this;
    }

    public function removeGallery(RoomGallery $gallery): self
    {
        if ($this->galleries->removeElement($gallery)) {
            // set the owning side to null (unless already changed)
            if ($gallery->getRoom() === $this) {
                $gallery->setRoom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setRoom($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getRoom() === $this) {
                $booking->setRoom(null);
            }
        }

        return $this;
    }

    public function getSupplementPrice(): int
    {
        $price = 0;

        /** @var Supplement $supplement */
        foreach ($this->supplements->toArray() as $supplement) {
            $price += (int) $supplement->getPrice();
        }

        return $price;
    }

    public function getOptionPrice(): int
    {
        $price = 0;

        foreach ($this->options->toArray() as $option) {
            foreach ($option->getSupplements()->toArray() as $supplement) {
                $price += (int) $supplement->getPrice();
            }
        }

        return $price;
    }
}

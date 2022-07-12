<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\PromotionRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PromotionRepository::class)]
#[Vich\Uploadable]
class Promotion
{
    use TimestampableTrait;
    use EnabledTrait;
    use MediaTrait;
    use PositionTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $name = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = '';

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\NotBlank]
    private ?DateTimeInterface $start = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\NotBlank]
    private ?DateTimeInterface $end = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $discount = null;

    #[Assert\File(maxSize: '8M')]
    #[Vich\UploadableField(mapping: 'promotion', fileNameProperty: 'fileName', size: 'fileSize', mimeType: 'fileMimeType', originalName: 'originalName')]
    private ?File $file;

    #[ORM\ManyToOne(targetEntity: Room::class, inversedBy: 'promotions')]
    #[ORM\JoinColumn(nullable: false)]
    private $room;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStart(): ?DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(?DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(?DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(?int $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        if (null !== $file) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        return $this;
    }
}

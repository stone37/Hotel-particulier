<?php

namespace App\Data;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use App\Service\BookerService as Booker;
use Symfony\Component\Validator\Constraints as Assert;

class BookingData
{
    public DateTime $checkin;

    public DateTime $checkout;

    public int $adult = Booker::INIT_ADULT;

    public int $children = Booker::INIT_CHILDREN;

    public int $roomNumber = Booker::INIT_ROOM;

    public ?string $message = '';

    /**
     * @Assert\NotBlank(groups="booking")
     */
    public string $firstname = '';

    /**
     * @Assert\NotBlank(groups="booking")
     */
    public string $lastname = '';

    /**
     * @Assert\NotBlank(groups="booking")
     */
    public string $email = '';

    /**
     * @Assert\NotBlank(groups="booking")
     */
    public string $phone = '';

    public ?string $country = '';

    public ?string $city = '';

    public int $days;

    public int $amount;

    public int $taxeAmount = 0;

    public int $discountAmount = 0;

    public ?int $roomId;

    public ?int $optionId;

    public ?int $userId;

    /**
     * @Assert\Valid(groups="booking")
     */
    public ArrayCollection $occupants;

    public function __construct()
    {
        $this->checkin = new DateTime();
        $this->checkout = (new DateTime())->modify('+1 day');
        $this->occupants = new ArrayCollection();
    }
}


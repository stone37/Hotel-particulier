<?php
namespace App\Util;

use App\Entity\Room;
use App\Storage\BookingSessionStorage;
use DateTime;

class SimpleRoomPriceCalculator
{
    private BookingSessionStorage $storage;
    private BookingDaysCalculator $daysCalculator;

    public function __construct(BookingSessionStorage $storage, BookingDaysCalculator $daysCalculator)
    {
        $this->storage = $storage;
        $this->daysCalculator = $daysCalculator;
    }

    public function getUnitPrice(Room $room)
    {
        $calculate = $this->price($room);

        if ($room->getTaxe() && $room->isTaxeStatus()) {
            $calculate = $this->price($room) - $this->taxe($room);
        }

        return $calculate;
    }

    public function getTotalUnitPrice(Room $room)
    {
        $calculate = $this->price($room);

        if ($room->getTaxe() && $room->isTaxeStatus()) {
            $calculate = $this->price($room) - $this->taxe($room);
        }

        $calculate = ($room->getTaxe()) ? ($calculate + $this->taxe($room)) : $calculate;

        return $calculate;
    }

    /**
     * @param Room $room
     * @return float|int
     */
    public function getPrice(Room $room)
    {
        return $this->calculator($this->getUnitPrice($room));
    }

    /**
     * @param Room $room
     * @return float|int
     */
    public function getTotalPrice(Room $room)
    {
        return $this->calculator($this->getTotalUnitPrice($room));
    }

    /**
     * @param Room $room
     * @return int
     */
    public function price(Room $room): int
    {
        return $room->getPrice() + $room->getSupplementPrice();
    }

    /**
     * @param Room $room
     * @return float|int
     */
    public function taxe(Room $room)
    {
        return ($this->price($room)*$room->getTaxe()->getValue()/100);
    }

    public function getDays(DateTime $checkin, DateTime $checkout): int
    {
        return $this->daysCalculator->getDays($checkin, $checkout);
    }

    public function calculator(int $calculate): int
    {
        if (!$this->storage->has()) {
            return $calculate;
        }

        $data = $this->storage->getBookingData();
        $calculate = ($calculate * $data->roomNumber * $this->getDays($data->checkin, $data->checkout));

        return $calculate;
    }
}

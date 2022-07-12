<?php
namespace App\Util;

use App\Entity\Option;
use App\Entity\Room;
use App\Storage\BookingSessionStorage;
use DateTime;

class RoomPriceCalculator
{
    private BookingSessionStorage $storage;
    private BookingDaysCalculator $daysCalculator;

    public function __construct(BookingSessionStorage $storage, BookingDaysCalculator $daysCalculator)
    {
        $this->storage = $storage;
        $this->daysCalculator = $daysCalculator;
    }

    public function getUnitPrice(Room $room, Option $option)
    {
        $calculate = $this->price($room, $option);

        if ($room->getTaxe() && $room->isTaxeStatus()) {
            $calculate = $this->price($room, $option) - $this->taxe($room, $option);
        }

        return $calculate;
    }

    public function getTotalUnitPrice(Room $room, Option $option)
    {
        $calculate = $this->price($room, $option);

        if ($room->getTaxe() && $room->isTaxeStatus()) {
            $calculate = $this->price($room, $option) - $this->taxe($room, $option);
        }

        $calculate = ($room->getTaxe()) ? ($calculate + $this->taxe($room, $option)) : $calculate;

        return $calculate;
    }

    /**
     * @param Room $room
     * @param Option $option
     * @return float|int
     */
    public function getPrice(Room $room, Option $option)
    {
        return $this->calculator($this->getUnitPrice($room, $option));
    }

    /**
     * @param Room $room
     * @param Option $option
     * @return float|int
     */
    public function getTotalPrice(Room $room, Option $option)
    {
        return $this->calculator($this->getTotalUnitPrice($room, $option));
    }

    /**
     * @param Room $room
     * @return float|int|mixed|void|null
     */
    public function getFirstUnitPrice(Room $room)
    {
        $numbers = [];

        if (!$room->getOptions()) {
            return;
        }

        foreach ($room->getOptions() as $option) {
            $numbers[] = $option->getOptionPrice();
        }

        $optionPriceMin = min($numbers);
        $calculate = $room->getPrice() + $optionPriceMin;

        if ($room->getTaxe() && $room->isTaxeStatus()) {
            return $calculate;
        }

        $calculate = ($room->getTaxe()) ? ($calculate+($calculate*$room->getTaxe()->getValue())/100) : $calculate;

        return $calculate;
    }

    /**
     * @param Room $room
     * @return float|int|mixed|void|null
     */
    public function getFirstTotalPrice(Room $room)
    {
        return $this->calculator($this->getFirstUnitPrice($room));
    }

    public function price(Room $room, Option $option): int
    {
        return $room->getPrice() + $option->getOptionPrice();
    }

    /**
     * @param Room $room
     * @param Option $option
     * @return float|int
     */
    public function taxe(Room $room, Option $option)
    {
        return ($this->price($room, $option)*$room->getTaxe()->getValue()/100);
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
        $calculate = ($calculate * $data->roomNumber * $this->getDays($data->checkin, $data->checkin));

        return $calculate;
    }
}


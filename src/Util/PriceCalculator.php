<?php

namespace App\Util;

use App\Entity\Option;
use App\Entity\Room;
use App\Storage\BookingSessionStorage;
use DateTime;

class PriceCalculator
{
    private BookingSessionStorage $storage;
    private BookingDaysCalculator $daysCalculator;
    private PromotionPriceCalculator $promotionPriceCalculator;

    public function __construct(
        BookingSessionStorage $storage,
        BookingDaysCalculator $daysCalculator,
        PromotionPriceCalculator $promotionPriceCalculator
    )
    {
        $this->storage = $storage;
        $this->daysCalculator = $daysCalculator;
        $this->promotionPriceCalculator = $promotionPriceCalculator;
    }

    public function getRealPrice(Room $room, Option $option = null): int
    {
        $calculate = $this->getPrice($room, $option);

        if ($room->getTaxe() && $room->isTaxeStatus()) {
            $calculate = $calculate - $this->getTaxe($room, $option);
        }

        return $calculate;
    }

    public function getTotalRealPrice(Room $room, Option $option = null): int
    {
        $calculate = $this->getRealPrice($room, $option);

        $calculate = $room->getTaxe() ? ($calculate + $this->getTaxe($room, $option)) : $calculate;

        return $calculate;
    }

    public function getPromotionRealPrice(Room $room,  int $reduction, Option $option = null): int
    {
        $calculate = $this->getPrice($room, $option);

        if ($room->getTaxe() && $room->isTaxeStatus()) {
            $calculate = $calculate - $this->getTaxe($room, $option);
        }

        return $this->promotionPriceCalculator->calculate($calculate, $reduction);
    }

    public function getTotalPromotionRealPrice(Room $room, int $reduction, Option $option = null): int
    {
        $calculate = $this->getPromotionRealPrice($room, $reduction, $option);

        $calculate = $room->getTaxe() ? ($calculate + $this->getTaxe($room, $option)) : $calculate;

        return $calculate;
    }

    public function getFirstPrice(Room $room): int
    {
        $numbers = [];

        if (!$room->getOptions()) {
            return 0;
        }

        foreach ($room->getOptions() as $option) {
            $numbers[] = $option->getOptionPrice();
        }

        $optionPriceMin = min($numbers);
        $calculate = $room->getPrice() + $optionPriceMin;

        if ($room->getTaxe() && $room->isTaxeStatus()) {
            return $calculate;
        }

        $calculate = $room->getTaxe() ? ($calculate + ($calculate*$room->getTaxe()->getValue()) / 100) : $calculate;

        return $calculate;
    }

    /**
     * @param Room $room
     * @return float|int|mixed|void|null
     */
    public function getFirstTotalPrice(Room $room)
    {
        return $this->calculator($this->getFirstPrice($room));
    }

    public function getPriceByBooking(Room $room, $option = null)
    {
        return $this->calculator($this->getRealPrice($room, $option));
    }

    public function getPriceTotalByBooking(Room $room, $option = null)
    {
        return $this->calculator($this->getTotalRealPrice($room, $option));
    }

    public function getPromotionPriceByBooking(Room $room, int $reduction, $option = null)
    {
        return $this->calculator($this->getPromotionRealPrice($room, $reduction, $option));
    }

    public function getPromotionPriceTotalByBooking(Room $room, int $reduction, $option = null)
    {
        return $this->calculator($this->getTotalPromotionRealPrice($room, $reduction, $option));
    }

    public function getPromotionFirstTotalPrice(Room $room, int $reduction)
    {
        $numbers = [];

        if (!$room->getOptions()) {
            return 0;
        }

        foreach ($room->getOptions() as $option) {
            $numbers[] = $option->getOptionPrice();
        }

        $optionPriceMin = min($numbers);
        $calculate = $room->getPrice() + $optionPriceMin;

        if ($room->getTaxe() && $room->isTaxeStatus()) {
            $calculate = $calculate - (($this->getPrice($room) * $room->getTaxe()->getValue()) / 100);
        }

        $taxe = $calculate * $room->getTaxe()->getValue() / 100;
        $calculate = $this->promotionPriceCalculator->calculate($calculate, $reduction);

        $calculate = $room->getTaxe() ? ($calculate + $taxe) : $calculate;

        return $this->calculator($calculate);
    }

    public function getPrice(Room $room, Option $option = null): int
    {
        if ($option) {
            return $room->getPrice() + $option->getOptionPrice();
        }

        return $room->getPrice() + $room->getSupplementPrice();
    }

    public function getTaxe(Room $room, Option $option = null): int
    {
        if ($option) {
            return (($this->getPrice($room, $option) * $room->getTaxe()->getValue()) / 100);
        }

        return (($this->getPrice($room) * $room->getTaxe()->getValue()) / 100);
    }

    private function getDays(DateTime $checkin, DateTime $checkout): int
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
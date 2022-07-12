<?php

namespace App\Twig;

use App\Entity\Option;
use App\Entity\Room;
use App\Util\RoomPriceCalculator;
use App\Util\SimpleRoomPriceCalculator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RoomPriceExtension extends AbstractExtension
{
    private RoomPriceCalculator $calculator;
    private SimpleRoomPriceCalculator $simpleCalculator;

    public function __construct(RoomPriceCalculator $calculator, SimpleRoomPriceCalculator $simpleCalculator)
    {
        $this->calculator = $calculator;
        $this->simpleCalculator = $simpleCalculator;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('app_room_price', array($this, 'getPrice')),
            new TwigFunction('app_room_total_price', array($this, 'getTotalPrice')),
            new TwigFunction('app_room_unit_price', array($this, 'getUnitPrice')),
            new TwigFunction('app_room_total_unit_price', array($this, 'getTotalUnitPrice')),
            new TwigFunction('app_room_first_unit_price', array($this, 'getFirstUnitPrice')),
            new TwigFunction('app_room_first_total_price', array($this, 'getFirstTotalPrice')),
            new TwigFunction('app_room_simple_unit_price', array($this, 'getSimpleUnitPrice')),
            new TwigFunction('app_room_simple_total_unit_price', array($this, 'getSimpleTotalUnitPrice')),
            new TwigFunction('app_room_simple_price', array($this, 'getSimplePrice')),
            new TwigFunction('app_room_simple_total_price', array($this, 'getSimpleTotalPrice')),
        );
    }

    public function getUnitPrice(Room $room, Option $option): int
    {
        return $this->calculator->getUnitPrice($room, $option);
    }

    public function getPrice(Room $room, Option $option): int
    {
        return $this->calculator->getPrice($room, $option);
    }

    public function getTotalUnitPrice(Room $room, Option $option): int
    {
        return $this->calculator->getTotalUnitPrice($room, $option);
    }

    public function getTotalPrice(Room $room, Option $option): int
    {
        return $this->calculator->getTotalPrice($room, $option);
    }

    public function getFirstUnitPrice(Room $room): int
    {
        return $this->calculator->getFirstUnitPrice($room);
    }

    public function getFirstTotalPrice(Room $room): int
    {
        return $this->calculator->getFirstTotalPrice($room);
    }

    public function getSimpleTotalUnitPrice(Room $room)
    {
        return $this->simpleCalculator->getTotalUnitPrice($room);
    }

    public function getSimpleUnitPrice(Room $room)
    {
        return $this->simpleCalculator->getUnitPrice($room);
    }

    public function getSimplePrice(Room $room): int
    {
        return $this->simpleCalculator->getPrice($room);
    }

    public function getSimpleTotalPrice(Room $room): int
    {
        return $this->simpleCalculator->getTotalPrice($room);
    }
}
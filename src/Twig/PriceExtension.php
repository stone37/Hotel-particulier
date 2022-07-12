<?php

namespace App\Twig;

use App\Entity\Option;
use App\Entity\Room;
use App\Util\PriceCalculator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PriceExtension extends AbstractExtension
{
    private PriceCalculator $calculator;

    public function __construct(PriceCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('room_real_price', array($this, 'getRealPrice')),
            new TwigFunction('room_total_real_price', array($this, 'getTotalRealPrice')),
            new TwigFunction('room_price_by_booking', array($this, 'getPriceByBooking')),
            new TwigFunction('room_price_total_by_booking', array($this, 'getPriceTotalByBooking')),
            new TwigFunction('room_first_price', array($this, 'getFirstPrice')),
            new TwigFunction('room_first_total_price', array($this, 'getFirstTotalPrice')),
            new TwigFunction('room_promotion_first_total_price', array($this, 'getPromotionFirstTotalPrice')),
            new TwigFunction('room_promotion_price_total_by_booking', array($this, 'getPromotionPriceTotalByBooking')),
            new TwigFunction('room_promotion_price_by_booking', array($this, 'getPromotionPriceByBooking')),
        );
    }

    public function getRealPrice(Room $room, Option $option = null)
    {
        return $this->calculator->getRealPrice($room, $option);
    }

    public function getTotalRealPrice(Room $room, Option $option = null)
    {
        return $this->calculator->getTotalRealPrice($room, $option);
    }

    public function getPriceByBooking(Room $room, Option $option = null)
    {
        return $this->calculator->getPriceByBooking($room, $option);
    }

    public function getPriceTotalByBooking(Room $room, Option $option = null)
    {
        return $this->calculator->getPriceTotalByBooking($room, $option);
    }

    public function getPromotionPriceTotalByBooking(Room $room, int $reduction, Option $option = null)
    {
        return $this->calculator->getPromotionPriceTotalByBooking($room, $reduction, $option);
    }

    public function getPromotionPriceByBooking(Room $room, int $reduction, Option $option = null)
    {
        return $this->calculator->getPromotionPriceByBooking($room, $reduction, $option);
    }

    public function getPromotionFirstTotalPrice(Room $room, int $reduction)
    {
        return $this->calculator->getPromotionFirstTotalPrice($room, $reduction);
    }

    public function getFirstPrice(Room $room)
    {
        return $this->calculator->getFirstPrice($room);
    }

    public function getFirstTotalPrice(Room $room)
    {
        return $this->calculator->getFirstTotalPrice($room);
    }
}


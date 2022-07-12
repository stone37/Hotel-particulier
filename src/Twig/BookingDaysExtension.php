<?php

namespace App\Twig;

use App\Util\BookingDaysCalculator;
use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BookingDaysExtension extends AbstractExtension
{
    private BookingDaysCalculator $calculator;

    public function __construct(BookingDaysCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('app_booking_days', array($this, 'getDays')),
        );
    }

    public function getDays(DateTime $checkin, DateTime $checkout): int
    {
        return $this->calculator->getDays($checkin, $checkout);
    }
}
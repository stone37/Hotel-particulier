<?php

namespace App\Util;

use DateTime;

class BookingDaysCalculator
{
    public function getDays(DateTime $start, DateTime $end): int
    {
        $interval = date_diff($start, $end);

        return (int) $interval->format('%a');
    }
}
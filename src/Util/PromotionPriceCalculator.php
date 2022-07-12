<?php

namespace App\Util;

class PromotionPriceCalculator
{
    public function calculate(int $total, int $reduction): int
    {
        return ($total - ($total * ($reduction / 100)));
    }
}
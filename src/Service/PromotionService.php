<?php

namespace App\Service;

use App\Entity\Room;

class PromotionService
{
    public function has(Room $room): bool
    {
        if (!$room->getPromotions()) {return false;}

        return true;
    }
}
<?php

namespace App\Manager;

use App\Entity\Room;
use App\Repository\PromotionRepository;

class PromotionManager
{
    private PromotionRepository $repository;

    public function __construct(PromotionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function hasRoomPromotion(Room $room): bool
    {
        return ($this->repository->fetchRoomPromotion($room) === 0) ? false : true;
    }

    public function getRoomPromotion(Room $room): int
    {
        return $this->repository->fetchRoomPromotion($room);
    }
}

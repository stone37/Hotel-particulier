<?php

namespace App\Service;

use App\Entity\Option;
use App\Entity\Room;
use App\Manager\PromotionManager;
use App\Repository\OptionRepository;
use App\Repository\RoomRepository;
use App\Util\PriceCalculator;
use App\Util\RoomPriceCalculator;
use App\Util\SimpleRoomPriceCalculator;

class RoomService
{
    private RoomRepository $repository;
    private OptionRepository $optionRepository;
    private CartService $service;
    private PriceCalculator $calculator;
    private PromotionManager $manager;

    public function __construct(
        RoomRepository $repository,
        CartService $service,
        PriceCalculator $calculator,
        SimpleRoomPriceCalculator $simplePriceCalculator,
        RoomPriceCalculator $priceCalculator,
        OptionRepository $optionRepository,
        PromotionManager $manager
    )
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->priceCalculator = $priceCalculator;
        $this->simplePriceCalculator = $simplePriceCalculator;
        $this->optionRepository = $optionRepository;
        $this->calculator = $calculator;
        $this->manager = $manager;
    }

    public function getMaxAdult(): int
    {
        $result = $this->repository->getMaximumAdult();

        return $result ? (int) $result['maximumAdults'] : 0;
    }

    public function getMaxChildren(): int
    {
        $result = $this->repository->getMaximumChildren();

        return $result ?  (int) $result['maximumOfChildren'] : 0;
    }

    public function getSelectRoom(): ?Room
    {
        $cart = $this->service->get();

        if (!$cart) {
            return null;
        }

        return $this->repository->find($cart['room_id']);
    }

    public function getSelectOption(): ?Option
    {
        $cart = $this->service->get();

        if (!$cart) {
            return null;
        }

        if (!array_key_exists('option_id', $cart)) {
            return null;
        }

        return $this->optionRepository->find($cart['option_id']);
    }

   public function getRoomPrice(Room $room, Option $option = null): int
    {
        return $this->calculator->getPriceByBooking($room, $option);
    }

    public function getTaxePrice(Room $room, Option $option = null): int
    {
        return ($this->calculator->getPriceTotalByBooking($room, $option) - $this->calculator->getPriceByBooking($room, $option));
    }

    public function getDiscount(Room $room): int
    {
       return $this->manager->getRoomPromotion($room);
    }
}
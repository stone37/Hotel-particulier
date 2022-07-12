<?php

namespace App\Service;

use App\Data\BookingData;
use App\Entity\Option;
use App\Entity\Room;
use App\Entity\User;
use App\Repository\BookingRepository;
use App\Storage\BookingSessionStorage;
use App\Util\BookingDaysCalculator;
use App\Util\PromotionPriceCalculator;
use DateTime;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class BookerService
{
    const INIT_ADULT = 2;
    const INIT_CHILDREN = 0;
    const INIT_ROOM = 1;

    private BookingRepository $repository;
    private Security $security;
    private BookingSessionStorage $storage;
    private BookingDaysCalculator $daysCalculator;
    private RoomService $roomService;
    private PromotionPriceCalculator $promotionPriceCalculator;

    public function __construct(
        BookingRepository $repository,
        BookingSessionStorage $storage,
        Security $security,
        BookingDaysCalculator $daysCalculator,
        RoomService $roomService,
        PromotionPriceCalculator $promotionPriceCalculator
    ) {
        $this->repository = $repository;
        $this->storage = $storage;
        $this->security = $security;
        $this->daysCalculator = $daysCalculator;
        $this->roomService = $roomService;
        $this->promotionPriceCalculator = $promotionPriceCalculator;
    }

    public function createData(Room $room, Option $option = null): BookingData
    {
        $data = $this->storage->getBookingData();
        $data->roomId = $room->getId();
        $data->optionId = ($option) ? $option->getId() : null;
        $data->days = $this->daysCalculator->getDays($data->checkin, $data->checkout);
        $data->amount = $this->roomPrice($room, $option);
        $data->taxeAmount = $this->roomTaxe($room, $option);
        $data->discountAmount = $data->amount - $this->discountCalculate($data->amount, $this->roomDiscount($room));

        if ($this->security->getUser()) {
            /** @var User|UserInterface $user */
            $user = $this->security->getUser();

            $data->firstname = (string) $user->getFirstName();
            $data->lastname = (string) $user->getLastName();
            $data->email = (string) $user->getEmail();
            $data->phone = (string) $user->getPhone();
            $data->country = (string) $user->getCountry();
            $data->city = (string) $user->getCity();
        }

        return $data;
    }

    public function add(BookingData $bookingData)
    {
        $this->storage->set($bookingData);
    }

    public function roomAvailableForPeriod(array $rooms)
    {
        $data = $this->storage->init();

        if (empty($rooms) || !$data) {
            return $rooms;
        }

        $results = [];

        /** @var Room $room */
        foreach ($rooms as $room) {
            if ($this->isAvailableForPeriod($room, $data->checkin, $data->checkout)) {
                $results[] = $room;
            }
        }

        return $results;
    }

    public function isAvailableForPeriod(Room $room, DateTime $start, DateTime $end)
    {
        $results = $this->repository->availableForPeriod($room, $start, $end);

        //return count($results) === 0;

        return ($room->getRoomNumber() > $results);
    }

    public function today(): DateTime
    {
        return new DateTime();
    }

    public function tomorrow(): DateTime
    {
        return (new DateTime())->modify('+1 day');
    }

    public function adjustDate(BookingData $data): BookingData
    {
        $data->checkin->modify("+{$this->storage->getCheckinMin()} minutes");
        $data->checkout->modify("+{$this->storage->getCheckoutMin()} minutes");

        return $data;
    }

    private function roomPrice(Room $room, Option $option = null): int
    {
        return $this->roomService->getRoomPrice($room, $option);
    }

    private function roomTaxe(Room $room, Option $option = null): int
    {
        return $this->roomService->getTaxePrice($room, $option);
    }

    private function roomDiscount(Room $room): int
    {
        return $this->roomService->getDiscount($room);
    }

    private function discountCalculate(int $amount, int $discount)
    {
        return $this->promotionPriceCalculator->calculate($amount, $discount);
    }
}


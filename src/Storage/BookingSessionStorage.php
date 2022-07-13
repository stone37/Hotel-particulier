<?php

namespace App\Storage;

use App\Data\BookingData;
use App\Entity\Settings;
use App\Manager\SettingsManager;
use DateTime;
use Symfony\Component\HttpFoundation\RequestStack;

class BookingSessionStorage
{
    private const BOOKING_KEY_NAME = 'booking_data';

    private RequestStack $request;
    private ?Settings $settings;

    public function __construct(SettingsManager $manager, RequestStack $request)
    {
        //$this->settings = $manager->get();
        $this->request = $request;
    }

    public function set(BookingData $data): void
    {
        $entity = $this->adjustDate($data);

        $data = [
            'checkin' => $entity->checkin,
            'checkout' => $entity->checkout,
            'adult' => $entity->adult,
            'children' => $entity->children,
            'room_nbr' => $entity->roomNumber
        ];

        $this->request->getSession()->set(self::BOOKING_KEY_NAME, $data);
    }

    public function remove(): void
    {
        $this->request->getSession()->remove(self::BOOKING_KEY_NAME);
    }


    public function getBookingData(): BookingData
    {
        if (!$this->has()) {
            return new BookingData();
        }

        return $this->init();
    }

    public function has(): bool
    {
        return $this->request->getSession()->has(self::BOOKING_KEY_NAME);
    }

    public function get(): ?array
    {
        return $this->request->getSession()->get(self::BOOKING_KEY_NAME);
    }

    private function adjustDate(BookingData $data): BookingData
    {
        $checkin = $data->checkin;
        $date = new DateTime();
        $date = $date->setTimestamp($checkin->getTimestamp());

        if ($data->checkout <= $data->checkin) {
            $data->checkout = $checkin->modify('+1 day');
            $data->checkin = $date;
        }

        return $data;
    }

    public function init(): ?BookingData
    {
        if (!$this->has()) {
            return null;
        }

        $session = $this->get();

        $data = new BookingData();
        $data->checkin = $session['checkin'];
        $data->checkout = $session['checkout'];
        $data->adult = $session['adult'];
        $data->children = $session['children'];
        $data->roomNumber = $session['room_nbr'];

        return $data;
    }

    public function getCheckinMin()
    {
        return (((int) $this->settings->getCheckinTime()->format('H') * 60) + (int) $this->settings->getCheckinTime()->format('i'));
    }

    public function getCheckoutMin()
    {
        return (((int) $this->settings->getCheckoutTime()->format('H') * 60) + (int) $this->settings->getCheckoutTime()->format('i'));
    }
}
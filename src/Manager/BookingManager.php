<?php

namespace App\Manager;

use App\Entity\Booking;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class BookingManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function confirm(Booking $booking)
    {
        $this->confirmed($booking);
        $this->em->flush();
    }

    public function cancel(Booking $booking)
    {
        $this->cancelled($booking);
        $this->em->flush();
    }

    public function cancelledAjustement(array $bookings)
    {
        if (!$bookings) {
            return;
        }

        /** @var Booking $booking */
        foreach($bookings as $booking) {
            if (!($booking->getStatus() === Booking::CANCELLED)) {
                $this->cancelled($booking);
            }
        }

        $this->em->flush();
    }

    private function confirmed(Booking $booking)
    {
        $booking->setStatus(Booking::CONFIRMED);
        $booking->setConfirmedAt(new DateTime());
        $booking->setCancelledAt(null);
    }

    private function cancelled(Booking $booking)
    {
        $booking->setStatus(Booking::CANCELLED);
        $booking->setCancelledAt(new DateTime());
        $booking->setConfirmedAt(null);
    }
}
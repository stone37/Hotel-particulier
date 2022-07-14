<?php

namespace App\Subscriber;

use App\Event\BookingPaymentEvent;
use App\Mailing\Mailer;
use App\Manager\SettingsManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class BookingPaymentSubscriber implements EventSubscriberInterface
{
    private Mailer $mailer;
    private SettingsManager $manager;

    public function __construct(Mailer $mailer, SettingsManager $manager)
    {
        $this->mailer = $mailer;
        $this->manager = $manager;
    }

    public static function getSubscribedEvents(): array
    {
        return [BookingPaymentEvent::class => 'onValidate'];
    }

    public function onValidate(BookingPaymentEvent $event)
    {
        $email = $this->mailer->createEmail('mails/commande/validate.twig', ['booking' => $event->getBooking()])
            ->to($event->getBooking()->getEmail())
            ->subject($this->manager->get()->getName().' | Validation de votre commande');

        $this->mailer->sendNow($email);
    }
}




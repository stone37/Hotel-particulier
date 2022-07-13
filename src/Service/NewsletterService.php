<?php

namespace App\Service;

use App\Entity\Emailing;
use App\Entity\Settings;
use App\Mailing\Mailer;
use App\Manager\SettingsManager;

class NewsletterService
{
    private Mailer $mailer;
    private ?Settings $settings;

    public function __construct(Mailer $mailer, SettingsManager $manager)
    {
        $this->mailer = $mailer;
        //$this->settings = $manager->get();
    }

    public function sendEmailing(Emailing $emailing)
    {
        $sender = $this->mailer->createEmail('mails/newsletter/particulier.twig', ['emailing' => $emailing])
            ->to($emailing->getDestinataire())
            ->subject($this->settings->getName() . ' | '. $emailing->getSubject());

        $this->mailer->sendNow($sender);
    }

    public function sendUserEmailing(Emailing $emailing, array $users)
    {
        foreach ($users as $user) {
            $sender = $this->mailer->createEmail('mails/newsletter/user.twig', [
                'emailing' => $emailing,
                'user' => $user
            ])->to($user->getEmail())
                ->subject($this->settings->getName() . ' | ' . $emailing->getSubject());

            $this->mailer->sendNow($sender);
        }
    }

    public function sendNewsletterEmailing(Emailing $emailing, array $newsletters)
    {
        foreach ($newsletters as $newsletter) {
            $sender = $this->mailer->createEmail('mails/newsletter/newsletter.twig', [
                'emailing' => $emailing,
                'newsletter' => $newsletter])
                ->to($newsletter->getEmail())
                ->subject($this->settings->getName() . ' | ' . $emailing->getSubject());

            $this->mailer->sendNow($sender);
        }
    }
}
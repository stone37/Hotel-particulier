<?php

namespace App\Handler;

use App\Entity\NewsletterData;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class NewsletterSubscriptionHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function subscribe(string $email)
    {
        $newsletterData = $this->em->getRepository(NewsletterData::class)->findOneBy(['email' => $email]);

        if ($newsletterData instanceof NewsletterData) {
            return false;
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user instanceof User) {
            $this->updateUser($user);
        }

        $this->createNewsletter($email);

        return true;
    }

    public function unsubscribe(NewsletterData $data)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $data->getEmail()]);

        if ($user instanceof User) {
            $this->updateUser($user, false);
        }

        $this->deleteNewsletter($data);
    }

    private function createNewsletter($email)
    {
        $newsletter = (new NewsletterData())->setEmail($email);

        $this->em->persist($newsletter);
        $this->em->flush();
    }

    private function deleteNewsletter(NewsletterData $data)
    {
        $this->em->remove($data);
        $this->em->flush();
    }


    private function updateUser(User $user, $subscribedToNewsletter = true)
    {
        $user->setSubscribedToNewsletter($subscribedToNewsletter);
        $this->em->flush();
    }
}


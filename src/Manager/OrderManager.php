<?php

namespace App\Manager;

use App\Entity\Commande;
use App\Entity\Discount;
use App\Event\OrderEvent;
use App\Service\Summary;
use App\Storage\CommandeSessionStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Security;

class OrderManager
{
    private Security $security;
    private EventDispatcherInterface $dispatcher;
    private EntityManagerInterface $em;
    private CommandeSessionStorage $storage;

    private Commande $commande;

    public function __construct(
        Security $security,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $em,
        CommandeSessionStorage $storage
    )
    {
        $this->security = $security;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->storage = $storage;

        $this->commande = $this->getCurrent();
    }

    public function getCurrent(): Commande
    {
        $commande = $this->storage->getCommande();

        if ($commande !== null) {
            return $commande;
        }

        $commande = new Commande();

        if ($this->security->getUser()) {
            $commande->setUser($this->security->getUser());
        }

        return $commande;
    }

    public function setDiscount(Discount $discount): void
    {
        if ($this->commande) {
            $this->commande->setDiscount($discount);

            $this->dispatcher->dispatch(new OrderEvent($this->commande));
            $this->em->persist($this->commande);

            $this->em->flush();
        }
    }

    public function summary(): Summary
    {
        return new Summary($this->commande);
    }
}
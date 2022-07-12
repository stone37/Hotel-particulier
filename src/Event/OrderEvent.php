<?php


namespace App\Event;

use App\Entity\Commande;

class OrderEvent
{
    private Commande $commande;

    public function __construct(Commande $Commande)
    {
        $this->commande = $Commande;
    }

    public function getCommande(): Commande
    {
        return $this->commande;
    }
}


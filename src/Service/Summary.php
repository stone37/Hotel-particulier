<?php

namespace App\Service;

use App\Entity\Commande;
use App\Entity\Discount;

class Summary
{
    private Commande $commande;

    public function __construct(Commande $commande)
    {
        $this->commande = $commande;
    }

    public function getBooking()
    {
        return $this->commande->getBooking();
    }

    public function getAmountTotal(): int
    {
        return $this->commande->getAmountTotal();
    }

    public function getAmountBeforeDiscount(): int
    {
        return $this->commande->getAmount();
    }

    public function getTaxeAmount(): int
    {
        return $this->commande->getTaxeAmount();
    }

    public function amountPaid()
    {
        return ($this->commande->getAmountTotal() - $this->getDiscount());
    }

    public function getDiscount(): int
    {
        $price = 0;
        $discount = $this->commande->getDiscount();

        if ($discount) {
            if ($discount->getType() === Discount::FIXED_PRICE) {
                $price = ($this->getAmountBeforeDiscount() - $discount->getDiscount());
            } elseif ($discount->getType() === Discount::PERCENT_REDUCTION) {
                $price = (($this->getAmountBeforeDiscount() * $discount->getDiscount()) / 100);
            }
        }

        return $price;
    }

    public function hasDiscount(): bool
    {
        return (bool) $this->commande->getDiscount();
    }

    public function getCommande(): Commande
    {
        return $this->commande;
    }
}

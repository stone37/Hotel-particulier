<?php

namespace App\Storage;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CommandeSessionStorage
{
    private const ORDER_KEY_NAME = 'orderId';

    private CommandeRepository $repository;
    private RequestStack $request;

    public function __construct(CommandeRepository $repository, RequestStack $request)
    {
        $this->repository = $repository;
        $this->request = $request;
    }

    public function set(string $orderId): void
    {
        $this->request->getSession()->set(self::ORDER_KEY_NAME, $orderId);
    }

    public function remove(): void
    {
        $this->request->getSession()->remove(self::ORDER_KEY_NAME);
    }

    public function getCommande(): ?Commande
    {
        if ($this->has()) {
            return $this->repository->find($this->get());
        }

        return null;
    }

    public function has(): bool
    {
        return $this->request->getSession()->has(self::ORDER_KEY_NAME);
    }

    public function get(): string
    {
        return $this->request->getSession()->get(self::ORDER_KEY_NAME);
    }
}


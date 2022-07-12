<?php

namespace App\Service;

use App\Entity\Option;
use App\Entity\Room;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private RequestStack $request;

    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }

    public function get(): ?array
    {
        if (!$this->request->getSession()->has('app_cart')) {
            return null;
        }

        return $this->request->getSession()->get('app_cart');
    }

    public function add(Room $room, Option $option = null): void
    {
        if ($option) {
            $this->request->getSession()->set('app_cart', ['room_id' => $room->getId(), 'option_id' => $option->getId()]);
        } else {
            $this->request->getSession()->set('app_cart', ['room_id' => $room->getId()]);
        }
    }

    public function init(): void
    {
        $this->request->getSession()->remove('app_cart');
    }
}
<?php

namespace App\Twig;

use App\Entity\Room;
use App\Manager\PromotionManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PromotionExtension extends AbstractExtension
{
    private PromotionManager $manager;

    public function __construct(PromotionManager $manager)
    {
        $this->manager = $manager;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('get_promotion', array($this, 'getPromotion'))
        );
    }

    public function getPromotion(Room $room)
    {
       return $this->manager->getRoomPromotion($room);
    }
}
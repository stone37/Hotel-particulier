<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Entity\Gallery;
use App\Entity\Promotion;
use App\Entity\Room;
use App\Entity\RoomEquipment;
use App\Entity\RoomGallery;
use App\Service\CartService;
use App\Storage\BookingSessionStorage;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private EntityManagerInterface $em;
    private BookingSessionStorage $storage;
    private CartService $cartService;

    public function __construct(
        EntityManagerInterface $em,
        BookingSessionStorage $storage,
        CartService $cartService
    )
    {
        $this->em = $em;
        $this->storage = $storage;
        $this->cartService = $cartService;
    }

    #[Route(path: '/', name: 'app_home')]
    public function index(): Response
    {
        $this->storage->remove();
        $this->cartService->init();

        $galleries = $this->em->getRepository(Gallery::class)->getGalleries(5);
        $equipments = $this->em->getRepository(Equipment::class)->findBy([], ['position' => 'ASC'], 10);
        $roomEquipments = $this->em->getRepository(RoomEquipment::class)->findBy([], ['position' => 'ASC'], 10);
        $equipmentImages = $this->em->getRepository(Gallery::class)->getGalleries(10);
        $roomEquipmentImages = $this->em->getRepository(RoomGallery::class)->getGalleries(10);
        $promotions = $this->em->getRepository(Promotion::class)->findLimit(3);
        $rooms = $this->em->getRepository(Room::class)->getEnabled();

        return $this->render('site/home/index.html.twig', [
            'galleries' => $galleries,
            'equipments' => $equipments,
            'roomEquipments' => $roomEquipments,
            'equipmentImages' => $equipmentImages,
            'roomEquipmentImages' => $roomEquipmentImages,
            'promotions' => $promotions,
            'rooms' => $rooms,
            'today' => new DateTime(),
            'tomorrow' => (new DateTime())->modify('+1 day'),
        ]);
    }
}

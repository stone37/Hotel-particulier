<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Entity\EquipmentGroup;
use App\Model\RoomFilter;
use App\Repository\RoomRepository;
use App\Service\BookerService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class RoomController extends AbstractController
{
    use ControllerTrait;

    private EntityManagerInterface $em;
    private RoomRepository $repository;
    private Breadcrumbs $breadcrumbs;
    private PaginatorInterface $paginator;
    private BookerService $booker;

    public function __construct(
        EntityManagerInterface $em,
        RoomRepository $repository,
        Breadcrumbs $breadcrumbs,
        PaginatorInterface $paginator,
        BookerService $booker
    )
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->breadcrumbs = $breadcrumbs;
        $this->paginator = $paginator;
        $this->booker = $booker;
    }

    #[Route(path: '/hebergements', name: 'app_room_index')]
    public function index(Request $request): Response
    {
        $this->breadcrumb($this->breadcrumbs)->addItem('Nos hébergements');

        /*$this->session->remove('orderId');
        $this->session->remove('app_cart');
        $this->session->remove('booking');*/

        $filter = new RoomFilter();
        $filter = $this->hydrate($request, $filter);

        $rooms = $this->paginator->paginate(
            $this->booker->roomAvailableForPeriod($this->repository->getFilter($filter)),
            $request->query->getInt('page', 1),15);

        $equipments = $this->em->getRepository(EquipmentGroup::class)->getAll();

        return $this->render('site/room/index.html.twig', [
            'rooms' => $rooms,
            'equipments' => $equipments,
        ]);
    }

    #[Route(path: '/hebergements/{slug}', name: 'app_room_show')]
    public function show($slug): Response
    {
        $room = $this->repository->getBySlug($slug);

        if (!$room) throw $this->createNotFoundException('Cet hébergement n\'existe pas');

        $this->breadcrumb($this->breadcrumbs)
            ->addItem('Hébergements', $this->generateUrl('app_room_index'))
            ->addItem($room->getName());

        $groupEquipments = $this->em->getRepository(EquipmentGroup::class)->getAll();

        return $this->render('site/room/show.html.twig', [
            'room' => $room,
            'groupEquipments' => $groupEquipments,
        ]);
    }

    private function hydrate(Request $request, RoomFilter $filter)
    {
        if ($request->query->has('adult'))
            $filter->setAdult($request->query->get('adult'));
        if ($request->query->has('children'))
            $filter->setChildren($request->query->get('children'));

        return $filter;
    }
}

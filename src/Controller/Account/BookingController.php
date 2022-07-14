<?php

namespace App\Controller\Account;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Booking;
use App\Form\Filter\BookingFilterType;
use App\Manager\BookingManager;
use App\Model\BookingSearch;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class BookingController extends AbstractController
{
    use ControllerTrait;

    private BookingRepository $bookingRepository;
    private PaginatorInterface $paginator;
    private EntityManagerInterface $em;
    private BookingManager $manager;

    public function __construct(
        BookingRepository $bookingRepository,
        PaginatorInterface $paginator,
        EntityManagerInterface $em,
        BookingManager $bookingManager
    )
    {
        $this->bookingRepository = $bookingRepository;
        $this->paginator = $paginator;
        $this->em = $em;
        $this->manager = $bookingManager;
    }

    #[Route(path: '/u/bookings', name: 'app_dashboard_booking_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request)
    {
        $search = new BookingSearch();
        $user = $this->getUserOrThrow();

        $form = $this->createForm(BookingFilterType::class, $search);
        $form->handleRequest($request);

        $bookings = $this->bookingRepository->getByUser($search, $user, Booking::NEW);
        $bookings = $this->paginator->paginate($bookings, $request->query->getInt('page', 1), 20);

        return $this->render('user/booking/index.html.twig', [
            'form' => $form->createView(),
            'bookings' => $bookings
        ]);
    }

    #[Route(path: '/u/bookings/confirmed', name: 'app_dashboard_booking_confirmed_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function confirm(Request $request)
    {
        $search = new BookingSearch();
        $user = $this->getUserOrThrow();

        $form = $this->createForm(BookingFilterType::class, $search);
        $form->handleRequest($request);

        $bookings = $this->bookingRepository->getByUser($search, $user, Booking::CONFIRMED);
        $bookings = $this->paginator->paginate($bookings, $request->query->getInt('page', 1), 20);

        return $this->render('user/booking/confirm.html.twig', [
            'form' => $form->createView(),
            'bookings' => $bookings
        ]);
    }

    #[Route(path: '/u/bookings/cancelled', name: 'app_dashboard_booking_cancel_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function cancel(Request $request)
    {
        $search = new BookingSearch();
        $user = $this->getUserOrThrow();

        $form = $this->createForm(BookingFilterType::class, $search);
        $form->handleRequest($request);

        $this->manager->cancelledAjustement($this->bookingRepository->getByUser($search, $user));

        $bookings = $this->bookingRepository->getByUser($search, $user, Booking::CANCELLED);

        $bookings = $this->paginator->paginate($bookings, $request->query->getInt('page', 1), 20);

        return $this->render('user/booking/cancel.html.twig', [
            'form' => $form->createView(),
            'bookings' => $bookings
        ]);
    }
}

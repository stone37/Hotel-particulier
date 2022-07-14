<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Data\BookingData;
use App\Entity\Commande;
use App\Entity\Room;
use App\Form\BookingDataType;
use App\Form\BookingType;
use App\Form\DiscountType;
use App\Manager\OrderManager;
use App\Repository\OptionRepository;
use App\Service\BookerService;
use App\Service\CartService;
use App\Service\RoomService;
use App\Service\Summary;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class BookingController extends AbstractController
{
    use ControllerTrait;

    private BookerService $booker;
    private CartService $cartService;
    private RoomService $roomService;
    private OptionRepository $optionRepository;
    private EntityManagerInterface $em;
    private OrderManager $manager;
    private Breadcrumbs $breadcrumbs;

    public function __construct(
        BookerService $booker,
        CartService $cartService,
        OptionRepository $optionRepository,
        RoomService $roomService,
        EntityManagerInterface $em,
        OrderManager $orderManager,
        Breadcrumbs $breadcrumbs
    ) {
        $this->booker = $booker;
        $this->cartService = $cartService;
        $this->roomService = $roomService;
        $this->optionRepository = $optionRepository;
        $this->em = $em;
        $this->manager = $orderManager;
        $this->breadcrumbs = $breadcrumbs;
    }

    #[Route(path: '/reservation', name: 'app_booking_index')]
    public function index(Request $request)
    {
        $this->breadcrumb($this->breadcrumbs)
            ->addItem('Hébergements', $this->generateUrl('app_room_index'))
            ->addItem('Réservation');

        $room = $this->roomService->getSelectRoom();
        $option = $this->roomService->getSelectOption();
        $booking = $this->booker->createData($room, $option);

        $prepareCommande = $this->forward('App\Controller\CommandeController::prepareCommande', [
            'data' => $booking
        ]);

        $commande = $this->em->getRepository(Commande::class)->find($prepareCommande->getContent());
        $summary = new Summary($commande);

        $bookingForm = $this->createForm(BookingType::class, $booking);
        $discountForm = $this->createForm(DiscountType::class, $commande);

        $bookingForm->handleRequest($request);

        if ($bookingForm->isSubmitted() && $bookingForm->isValid()) {

            $request->getSession()->set('booking', $this->booker->adjustDate($booking));
            //dd($this->session->get('booking'));
            // active le paiement

            //$this->manager->addItem($booking);
            //$this->em->persist($booking);
            //$this->em->flush();

            return $this->redirectToRoute('app_commande_pay');
        } else if ($bookingForm->isSubmitted()) {
            $this->addFlash('error', 'Un ou plusieurs champs n\'ont pas été renseigne');
        }

        return $this->render('site/booking/index.html.twig', [
            'booking_form' => $bookingForm->createView(),
            'discount_form' => $discountForm->createView(),
            'commande' => $summary,
            'booking' => $booking,
            'room' => $room,
        ]);
    }

    #[Route(path: '/reservation/search', name: 'app_booking_search')]
    public function search(Request $request)
    {
        $data = new BookingData();

        $form = $this->createForm(BookingDataType::class, $data, [
            'action' => $this->generateUrl('app_booking_search')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->booker->add($data);

            return $this->redirectToRoute('app_room_index', [
                'adult' => $data->adult,
                'children' => $data->children
            ]);
        } else if ($form->isSubmitted()) {
            return $this->redirectToRoute('app_room_index');
        }

        return $this->render('site/booking/search.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/reservation/select', name: 'app_booking_select')]
    public function select(Request $request)
    {
        $data = new BookingData();

        $form = $this->createForm(BookingDataType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->booker->add($data);

            if ($this->booker->isAvailableForPeriod($this->roomService->getSelectRoom(), $data->checkin, $data->checkin)) {
                return $this->redirectToRoute('app_booking_index');
            } else {
                $this->addFlash('error', "L'hébergement que vous aviez choisis est complet pour cette periode.");

                return $this->redirectToRoute('app_booking_select');
            }

        }

        return $this->render('site/booking/select.html.twig', [
            'form' => $form->createView(),
            'room' => $this->roomService->getSelectRoom()
        ]);
    }

    #[Route(path: '/reservation/{id}/check', name: 'app_booking_check', requirements: ['id' => '\d+'])]
    public function check(Request $request, Room $room)
    {
        if ($request->query->has('option_id')) {
            $this->cartService->add($room, $this->optionRepository->find($request->query->get('option_id')));
        } else {
            $this->cartService->add($room);
        }

        return ($request->getSession()->has('booking_data')) ?
            $this->redirectToRoute('app_booking_index') :
            $this->redirectToRoute('app_booking_select');
    }
}

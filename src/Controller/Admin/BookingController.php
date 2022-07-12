<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\User;
use App\Event\AdminCRUDEvent;
use App\Event\BookingCancelledEvent;
use App\Event\BookingConfirmedEvent;
use App\Form\Filter\AdminBookingType;
use App\Manager\BookingManager;
use App\Model\BookingSearch;
use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    private BookingRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;
    private BookingManager $manager;
    private BookingRepository $bookingRepository;

    public function __construct(
        BookingRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher,
        BookingManager $manager,
        BookingRepository $bookingRepository
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
        $this->manager = $manager;
        $this->bookingRepository = $bookingRepository;
    }

    #[Route(path: '/admin/bookings', name: 'app_admin_booking_index')]
    public function index(Request $request)
    {
        $search = new BookingSearch();

        $form = $this->createForm(AdminBookingType::class, $search);

        $form->handleRequest($request);

        $qb = $this->repository->getAdmins($search);

        $bookings = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $bookings,
            'searchForm' => $form->createView(),
            'type' => '1'
        ]);
    }

    #[Route(path: '/admin/bookings/confirmed', name: 'app_admin_booking_confirmed_index')]
    public function confirm(Request $request)
    {
        $search = new BookingSearch();

        $form = $this->createForm(AdminBookingType::class, $search);

        $form->handleRequest($request);

        $qb = $this->repository->getConfirmAdmins($search);

        $bookings = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $bookings,
            'searchForm' => $form->createView(),
            'type' => '2'
        ]);
    }

    #[Route(path: '/admin/bookings/cancelled', name: 'app_admin_booking_cancel_index')]
    public function cancel(Request $request)
    {
        $search = new BookingSearch();

        $form = $this->createForm(AdminBookingType::class, $search);

        $form->handleRequest($request);

        $this->manager->cancelledAjustement($this->bookingRepository->getCancel());

        $qb = $this->repository->getCancelAdmins($search);

        $bookings = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $bookings,
            'searchForm' => $form->createView(),
            'type' => '3'
        ]);
    }

    #[Route(path: '/admin/bookings/archive', name: 'app_admin_booking_archive_index')]
    public function archive(Request $request)
    {
        $search = new BookingSearch();

        $form = $this->createForm(AdminBookingType::class, $search);

        $form->handleRequest($request);

        $qb = $this->repository->getArchiveAdmins($search);

        $bookings = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $bookings,
            'searchForm' => $form->createView(),
            'type' => '4'
        ]);
    }

    #[Route(path: '/admin/bookings/{id}/show/{type}', name: 'app_admin_booking_show', requirements: ['id' => '\d+'])]
    public function show(Booking $booking, $type)
    {
        return $this->render('admin/booking/show.html.twig', [
            'booking' => $booking,
            'type' => $type,
        ]);
    }

    #[Route(path: '/admin/bookings/{id}/user', name: 'app_admin_booking_user', requirements: ['id' => '\d+'])]
    public function user(Request $request, User $user)
    {
        $search = (new BookingSearch())->setUser($user->getId());

        $form = $this->createForm(AdminBookingType::class, $search);
        $form->handleRequest($request);

        $qb = $this->repository->admins($search);

        $bookings = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $bookings,
            'searchForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/admin/bookings/{id}/room', name: 'app_admin_booking_room', requirements: ['id' => '\d+'])]
    public function room(Request $request, Room $room)
    {
        $search = (new BookingSearch())->setRoom($room->getId());

        $form = $this->createForm(AdminBookingType::class, $search);
        $form->handleRequest($request);

        $qb = $this->repository->getRepository(Booking::class)->admins($search);

        $bookings = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $bookings,
            'searchForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/admin/bookings/{id}/confirmed', name: 'app_admin_booking_confirmed', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function confirmed(Request $request, Booking $booking)
    {
        $form = $this->confirmedForm($booking);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $this->manager->confirm($booking);

                $this->dispatcher->dispatch(new BookingConfirmedEvent($booking));

                $this->addFlash('success', 'La reservation a été confirmer');
            } else {
                $this->addFlash('error', 'Désolé, la reservation n\'a pas pu être confirmer !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir confirmer cette reservation ?';

        $render = $this->render('Ui/Modal/_confirm.html.twig', [
            'form' => $form->createView(),
            'data' => $booking,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/admin/bookings/bulk/confirmed', name: 'app_admin_booking_bulk_confirmed', options: ['expose' => true])]
    public function confirmedBulk(Request $request)
    {
        $ids = (array)$request->query->get('data');

        if ($request->query->has('data')) {
            $request->getSession()->set('data', $request->query->get('data'));
        }

        $form = $this->confirmedMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids = $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $booking = $this->bookingRepository->find($id);

                    $this->manager->confirm($booking);

                    $this->dispatcher->dispatch(new BookingConfirmedEvent($booking));
                }

                $this->addFlash('success', 'Les reservations ont été confirmer');
            } else {
                $this->addFlash('error', 'Désolé, les reservations n\'ont pas pu être confirmer !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir confirmer ces '.count($ids).' reservations ?';
        else
            $message = 'Être vous sur de vouloir confirmer cette reservation ?';

        $render = $this->render('Ui/Modal/_confirm_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/admin/bookings/{id}/cancelled', name: 'app_admin_booking_cancelled', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function cancelled(Request $request, Booking $booking)
    {
        $form = $this->cancelledForm($booking);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $this->manager->cancel($booking);

                $this->dispatcher->dispatch(new BookingCancelledEvent($booking));

                $this->addFlash('success', 'La reservation a été annuler');
            } else {
                $this->addFlash('error', 'Désolé, la reservation n\'a pas pu être annuler !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir annuler cette reservation ?';

        $render = $this->render('Ui/Modal/_cancel.html.twig', [
            'form' => $form->createView(),
            'data' => $booking,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/admin/bookings/bulk/cancelled', name: 'app_admin_booking_bulk_cancelled', options: ['expose' => true])]
    public function cancelledBulk(Request $request)
    {
        $ids = (array)$request->query->get('data');

        if ($request->query->has('data')) {
            $request->getSession()->set('data', $request->query->get('data'));
        }

        $form = $this->cancelledMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $booking = $this->bookingRepository->find($id);

                    $this->manager->cancel($booking);

                    $this->dispatcher->dispatch(new BookingCancelledEvent($booking));
                }

                $this->addFlash('success', 'Les reservations ont été annuler');
            } else {
                $this->addFlash('error', 'Désolé, les reservations n\'ont pas pu être annuler !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir annuler ces '.count($ids).' reservations ?';
        else
            $message = 'Être vous sur de vouloir annuler cette reservation ?';

        $render = $this->render('Ui/Modal/_cancel_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(
        path: '/admin/bookings/{id}/delete', name: 'app_admin_booking_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Booking $booking)
    {
        $form = $this->deleteForm($booking);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($booking);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($booking);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'La reservation a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, la reservation n\'a pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cette reservation ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $booking,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/admin/bookings/bulk/delete', name: 'app_admin_booking_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(Request $request)
    {
        $ids = (array) $request->query->get('data');

        if ($request->query->has('data'))
            $request->getSession()->set('data', $request->query->get('data'));

        $form = $this->deleteMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids = $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $booking = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($booking), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($booking, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les reservations ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les reservations n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' reservations ?';
        else
            $message = 'Être vous sur de vouloir supprimer cette reservation ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function confirmedForm(Booking $booking)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_booking_confirmed', ['id' => $booking->getId()]))
            ->getForm();
    }

    private function confirmedMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_booking_bulk_confirmed'))
            ->getForm();
    }

    private function cancelledForm(Booking $booking)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_booking_cancelled', ['id' => $booking->getId()]))
            ->getForm();
    }

    private function cancelledMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_booking_bulk_cancelled'))
            ->getForm();
    }

    private function deleteForm(Booking $booking)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_booking_delete', ['id' => $booking->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_booking_bulk_delete'))
            ->getForm();
    }

    private function configuration()
    {
        return [
            'modal' => [
                'delete' => [
                    'type' => 'modal-danger',
                    'icon' => 'fas fa-times',
                    'yes_class' => 'btn-outline-danger',
                    'no_class' => 'btn-danger'
                ],
                'confirmed' => [
                    'type' => 'modal-success',
                    'icon' => 'fas fa-check',
                    'yes_class' => 'btn-outline-success',
                    'no_class' => 'btn-success'
                ],
                'cancelled' => [
                    'type' => 'modal-danger',
                    'icon' => 'fas fa-times',
                    'yes_class' => 'btn-outline-danger',
                    'no_class' => 'btn-danger'
                ],
            ]
        ];
    }
}



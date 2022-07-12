<?php

namespace App\Controller\Admin;

use App\Entity\Room;
use App\Event\AdminCRUDEvent;
use App\Form\Filter\AdminRoomType;
use App\Form\RoomType;
use App\Model\RoomSearch;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;

class RoomController extends AbstractController
{
    private RoomRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        RoomRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    #[Route(path: '/admin/rooms', name: 'app_admin_room_index')]
    public function index(Request $request)
    {
        $search = new RoomSearch();

        $form = $this->createForm(AdminRoomType::class, $search);
        $form->handleRequest($request);

        $qb = $this->repository->getAdmins($search);

        $rooms = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/room/index.html.twig', [
            'rooms' => $rooms,
            'searchForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/admin/rooms/show/{id}', name: 'app_admin_room_show', requirements: ['id' => '\d+'])]
    public function show(Room $room)
    {
       return $this->render('admin/room/show.html.twig', ['room' => $room]);
    }

    #[Route(path: '/admin/rooms/create', name: 'app_admin_room_create')]
    public function create(Request $request)
    {
        $room = (new Room())->setType('configured');

        $form = $this->createForm(RoomType::class, $room);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($room);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($room);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Un hébergement a été crée');

            return $this->redirectToRoute('app_admin_room_index');
        }

        return $this->render('admin/room/create.html.twig', [
            'form' => $form->createView(),
            'room' => $room,
        ]);
    }

    #[Route(path: '/admin/rooms/create/simple', name: 'app_admin_room_create_simple')]
    public function createSimple(Request $request)
    {
        $room = new Room();

        $form = $this->createForm(RoomType::class, $room);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($room);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($room);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Un hébergement a été crée');

            return $this->redirectToRoute('app_admin_room_index');
        }

        return $this->render('admin/room/create.html.twig', [
            'form' => $form->createView(),
            'room' => $room,
        ]);
    }

    #[Route(path: '/admin/rooms/{id}/edit', name: 'app_admin_room_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Room $room)
    {
        $form = $this->createForm(RoomType::class, $room);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($room);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Un hébergement a été mise à jour');

            return $this->redirectToRoute('app_admin_room_index');
        }

        return $this->render('admin/room/edit.html.twig', [
            'form' => $form->createView(),
            'room' => $room,
        ]);
    }

    #[Route(path: '/admin/rooms/{id}/move', name: 'app_admin_room_move', requirements: ['id' => '\d+'])]
    public function move(Request $request, Room $room)
    {
        if ($request->query->has('pos')) {
            $pos = ($room->getPosition() + (int)$request->query->get('pos'));

            if ($pos >= 0) {
                $room->setPosition($pos);
                $this->repository->flush();

                $this->addFlash('success', 'La position a été modifier');
            }
        }

        return $this->redirectToRoute('app_admin_room_index');
    }

    #[Route(path: '/admin/rooms/{id}/delete', name: 'app_admin_room_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Room $room)
    {
        $form = $this->deleteForm($room);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($room);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($room);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'L\'hébergement a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, l\'hébergement n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet hébergement ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $room,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/admin/rooms/bulk/delete', name: 'app_admin_room_bulk_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
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
                    $room = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($room), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($room, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les hébergements ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les hébergements n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' hébergements ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet hébergement ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Room $room)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_room_delete', ['id' => $room->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_room_bulk_delete'))
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
            ]
        ];
    }
}



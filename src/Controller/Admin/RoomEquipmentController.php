<?php

namespace App\Controller\Admin;

use App\Entity\RoomEquipment;
use App\Event\AdminCRUDEvent;
use App\Form\Filter\AdminEquipmentType;
use App\Form\RoomEquipmentType;
use App\Model\EquipmentSearch;
use App\Repository\RoomEquipmentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;;
use Symfony\Component\Routing\Annotation\Route;

class RoomEquipmentController extends AbstractController
{
    private RoomEquipmentRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        RoomEquipmentRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    #[Route(path: '/admin/room-equipments', name: 'app_admin_room_equipment_index')]
    public function index(Request $request)
    {
        $search = new EquipmentSearch();
        $form = $this->createForm(AdminEquipmentType::class, $search);

        $form->handleRequest($request);
        $qb = $this->repository->getAdmins($search);

        $equipments = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/roomEquipment/index.html.twig', [
            'equipments' => $equipments,
            'searchForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/admin/room-equipments/create', name: 'app_admin_room_equipment_create')]
    public function create(Request $request)
    {
        $equipment = new RoomEquipment();

        $form = $this->createForm(RoomEquipmentType::class, $equipment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($equipment);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($equipment);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Un équipement a été crée');

            return $this->redirectToRoute('app_admin_room_equipment_index');
        }

        return $this->render('admin/roomEquipment/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/admin/room-equipments/{id}/edit', name: 'app_admin_room_equipment_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, RoomEquipment $equipment)
    {
        $form = $this->createForm(RoomEquipmentType::class, $equipment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($equipment);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Un équipement a été mise à jour');

            return $this->redirectToRoute('app_admin_room_equipment_index');
        }

        return $this->render('admin/roomEquipment/edit.html.twig', [
            'form' => $form->createView(),
            'equipment' => $equipment,
        ]);
    }

    #[Route(path: '/admin/room-equipments/{id}/move', name: 'app_admin_room_equipment_move', requirements: ['id' => '\d+'])]
    public function move(Request $request, RoomEquipment $equipment)
    {
        if ($request->query->has('pos')) {
            $pos = ($equipment->getPosition() + (int)$request->query->get('pos'));

            if ($pos >= 0) {
                $equipment->setPosition($pos);
                $this->repository->flush();

                $this->addFlash('success', 'La position a été modifier');
            }
        }

        return $this->redirectToRoute('app_admin_room_equipment_index');
    }

    #[Route(path: '/admin/room-equipments/{id}/delete', name: 'app_admin_room_equipment_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, RoomEquipment $equipment)
    {
        $form = $this->deleteForm($equipment);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($equipment);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($equipment);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'L\'équipement  a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, l\'équipement n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet équipement ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $equipment,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/admin/room-equipments/bulk/delete', name: 'app_admin_room_equipment_bulk_delete', options: ['expose' => true])]
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
                    $equipment = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($equipment), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($equipment, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les équipements ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les équipements n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' équipements ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet équipement ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(RoomEquipment $equipment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_room_equipment_delete', ['id' => $equipment->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_room_equipment_bulk_delete'))
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
                ]
            ]
        ];
    }
}


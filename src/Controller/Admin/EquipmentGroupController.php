<?php

namespace App\Controller\Admin;

use App\Entity\EquipmentGroup;
use App\Event\AdminCRUDEvent;
use App\Form\EquipmentGroupType;
use App\Form\Filter\AdminEquipmentGroupType;
use App\Model\EquipmentGroupSearch;
use App\Repository\EquipmentGroupRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EquipmentGroupController extends AbstractController
{
    private EquipmentGroupRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        EquipmentGroupRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    #[Route(path: '/admin/equipments-group', name: 'app_admin_equipment_group_index')]
    public function index(Request $request)
    {
        $search = new EquipmentGroupSearch();
        $form = $this->createForm(AdminEquipmentGroupType::class, $search);

        $form->handleRequest($request);
        $qb = $this->repository->getAdmins($search);

        $equipmentGroup = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/equipmentGroup/index.html.twig', [
            'equipments' => $equipmentGroup,
            'searchForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/admin/equipments-group/create', name: 'app_admin_equipment_group_create')]
    public function create(Request $request)
    {
        $equipmentGroup = new EquipmentGroup();

        $form = $this->createForm(EquipmentGroupType::class, $equipmentGroup);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($equipmentGroup);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($equipmentGroup, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Un groupe d\'équipement a été crée');

            return $this->redirectToRoute('app_admin_equipment_group_index');
        }

        return $this->render('admin/equipmentGroup/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/admin/equipments-group/{id}/edit', name: 'app_admin_equipment_group_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, EquipmentGroup $equipmentGroup)
    {
        $form = $this->createForm(EquipmentGroupType::class, $equipmentGroup);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($equipmentGroup);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Un groupe d\'équipement a été mise à jour');

            return $this->redirectToRoute('app_admin_equipment_group_index');
        }

        return $this->render('admin/equipmentGroup/edit.html.twig', [
            'form' => $form->createView(),
            'equipment' => $equipmentGroup,
        ]);
    }

    #[Route(path: '/admin/equipments-group/{id}/delete', name: 'app_admin_equipment_group_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, EquipmentGroup $equipmentGroup)
    {
       $form = $this->deleteForm($equipmentGroup);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($equipmentGroup);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($equipmentGroup);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Le groupe d\'équipement a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le groupe d\'équipement n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet groupe d\'équipement ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $equipmentGroup,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/admin/equipments-group/bulk/delete', name: 'app_admin_equipment_group_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(Request $request)
    {
        $ids = (array) json_decode($request->query->get('data'));

        if ($request->query->has('data'))
            $request->getSession()->set('data', $ids);

        $form = $this->deleteMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids = $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $equipmentGroup = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($equipmentGroup), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($equipmentGroup, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les groupes d\'équipements ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les groupes d\'équipements n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' groupes d\'équipements ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet groupe d\'équipement ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(EquipmentGroup $equipmentGroup)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_equipment_group_delete', ['id' => $equipmentGroup->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_equipment_group_bulk_delete'))
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


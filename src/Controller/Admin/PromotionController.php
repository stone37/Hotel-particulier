<?php

namespace App\Controller\Admin;

use App\Entity\Promotion;
use App\Event\AdminCRUDEvent;
use App\Form\PromotionType;
use App\Repository\PromotionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;

class PromotionController extends AbstractController
{
    private PromotionRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        PromotionRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    #[Route(path: '/admin/promotions', name: 'app_admin_promotion_index')]
    public function index(Request $request)
    {
        $qb = $this->repository->findBy([], ['position' => 'asc']);

        $promotions = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/promotion/index.html.twig', [
            'promotions' => $promotions,
        ]);
    }

    #[Route(path: '/admin/promotions/create', name: 'app_admin_promotion_create')]
    public function create(Request $request)
    {
        $promotion = new Promotion();

        $form = $this->createForm(PromotionType::class, $promotion);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($promotion);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($promotion, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Une promotion a été crée');

            return $this->redirectToRoute('app_admin_promotion_index');
        }

        return $this->render('admin/promotion/create.html.twig', [
            'form' => $form->createView(),
            'promotion' => $promotion,
        ]);
    }

    #[Route(path: '/admin/promotions/{id}/edit', name: 'app_admin_promotion_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Promotion $promotion)
    {
        $form = $this->createForm(PromotionType::class, $promotion);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($promotion);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Une promotion a été mise à jour');

            return $this->redirectToRoute('app_admin_promotion_index');
        }

        return $this->render('admin/promotion/edit.html.twig', [
            'form' => $form->createView(),
            'promotion' => $promotion,
        ]);
    }

    #[Route(path: '/admin/promotions/{id}/move', name: 'app_admin_promotion_move', requirements: ['id' => '\d+'])]
    public function move(Request $request, Promotion $promotion)
    {
        if ($request->query->has('pos')) {
            $pos = ($promotion->getPosition() + (int)$request->query->get('pos'));

            if ($pos >= 0) {
                $promotion->setPosition($pos);
                $this->repository->flush();

                $this->addFlash('success', 'La position a été modifier');
            }
        }

        return $this->redirectToRoute('app_admin_promotion_index');
    }

    #[Route(path: '/admin/promotions/show/{id}', name: 'app_admin_promotion_show', requirements: ['id' => '\d+'])]
    public function show(Promotion $promotion)
    {
        return $this->render('admin/promotion/show.html.twig', ['promotion' => $promotion]);
    }

    #[Route(path: '/admin/promotions/{id}/delete', name: 'app_admin_promotion_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Promotion $promotion)
    {
        $form = $this->deleteForm($promotion);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($promotion);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($promotion);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'La promotion a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, la promotion n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cette promotion ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $promotion,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/admin/promotions/bulk/delete', name: 'app_admin_promotion_bulk_delete', options: ['expose' => true])]
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
                    $promotion = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($promotion), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($promotion, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les promotion ont été supprimé');
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
            $message = 'Être vous sur de vouloir supprimer cette promotion ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Promotion $promotion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_promotion_delete', ['id' => $promotion->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_promotion_bulk_delete'))
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



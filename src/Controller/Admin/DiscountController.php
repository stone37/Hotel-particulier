<?php

namespace App\Controller\Admin;

use App\Entity\Discount;
use App\Event\AdminCRUDEvent;
use App\Form\DiscountAdminType;
use App\Repository\DiscountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;

class DiscountController extends AbstractController
{
    private DiscountRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        DiscountRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    #[Route(path: '/admin/discounts', name: 'app_admin_discount_index')]
    public function index(Request $request)
    {
        $qb = $this->repository->getAdmin();

        $discounts = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/discount/index.html.twig', [
            'discounts' => $discounts,
        ]);
    }

    #[Route(path: '/admin/discounts/create', name: 'app_admin_discount_create')]
    public function create(Request $request)
    {
        $discount = new Discount();
        $form = $this->createForm(DiscountAdminType::class, $discount);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($discount);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($discount);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Un code de réduction a été crée');

            return $this->redirectToRoute('app_admin_discount_index');
        }

        return $this->render('admin/discount/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/admin/discounts/{id}/edit', name: 'app_admin_discount_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Discount $discount): Response
    {
        $form = $this->createForm(DiscountAdminType::class, $discount);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($discount);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Un code de réduction a été mise à jour');

            return $this->redirectToRoute('app_admin_discount_index');
        }

        return $this->render('admin/discount/edit.html.twig', [
            'form' => $form->createView(),
            'discount' => $discount,
        ]);
    }

    #[Route(path: '/admin/discounts/{id}/delete', name: 'app_admin_discount_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Discount $discount)
    {
        $form = $this->deleteForm($discount);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($discount);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($discount);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Le code de reduction a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le code de reduction n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet code de réduction ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $discount,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/admin/discounts/bulk/delete', name: 'app_admin_discount_bulk_delete', options: ['expose' => true])]
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
                    $discount = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($discount), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($discount, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les codes de réduction ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les codes de réduction n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' codes de réduction ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet code de réduction ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Discount $discount)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_discount_delete', ['id' => $discount->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_discount_bulk_delete'))
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



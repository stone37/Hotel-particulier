<?php

namespace App\Controller\Admin;

use App\Entity\Option;
use App\Event\AdminCRUDEvent;
use App\Form\OptionType;
use App\Repository\OptionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OptionController extends AbstractController
{
    private OptionRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        OptionRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    #[Route(path: '/admin/options', name: 'app_admin_option_index')]
    public function index(Request $request)
    {
        $qb = $this->repository->findBy([], ['position' => 'ASC']);

        $options = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/option/index.html.twig', [
            'options' => $options,
        ]);
    }

    #[Route(path: '/admin/options/create', name: 'app_admin_option_create')]
    public function create(Request $request)
    {
        $option = new Option();

        $form = $this->createForm(OptionType::class, $option);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($option);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($option);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Une option a été crée');

            return $this->redirectToRoute('app_admin_option_index');
        }

        return $this->render('admin/option/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/admin/options/{id}/edit', name: 'app_admin_option_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Option $option)
    {
        $form = $this->createForm(OptionType::class, $option);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($option);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Une option a été mise à jour');

            return $this->redirectToRoute('app_admin_option_index');
        }

        return $this->render('admin/option/edit.html.twig', [
            'form' => $form->createView(),
            'option' => $option,
        ]);
    }

    #[Route(path: '/admin/options/{id}/move', name: 'app_admin_option_move', requirements: ['id' => '\d+'])]
    public function move(Request $request, Option $option)
    {
        if ($request->query->has('pos')) {
            $pos = ($option->getPosition() + (int)$request->query->get('pos'));

            if ($pos >= 0) {
                $option->setPosition($pos);
                $this->repository->flush();

                $this->addFlash('success', 'La position a été modifier');
            }
        }

        return $this->redirectToRoute('app_admin_option_index');
    }

    #[Route(path: '/admin/options/{id}/delete', name: 'app_admin_option_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Option $option)
    {
        $form = $this->deleteForm($option);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($option);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($option);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'L\'option a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, option n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cette option ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $option,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/admin/options/bulk/delete', name: 'app_admin_option_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(Request $request)
    {
        $ids = (array)$request->query->get('data');

        if ($request->query->has('data'))
            $request->getSession()->set('data', $request->query->get('data'));

        $form = $this->deleteMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids = $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $option = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($option), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($option, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les options ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les options n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' options ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet options ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Option $option)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_option_delete', ['id' => $option->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_option_bulk_delete'))
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


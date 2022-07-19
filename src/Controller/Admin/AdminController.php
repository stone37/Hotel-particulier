<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Event\AdminCRUDEvent;
use App\Form\Filter\AdminUserType;
use App\Form\RegistrationAdminType;
use App\Model\UserSearch;
use App\Repository\UserRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class AdminController extends AbstractController
{
    private UserRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher,
        UserPasswordHasherInterface $passwordHasher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route(path: '/admin/admins', name: 'app_admin_admin_index')]
    public function index(Request $request)
    {
        $search = new UserSearch();

        $form = $this->createForm(AdminUserType::class, $search);

        $form->handleRequest($request);

        $qb = $this->repository->getAdmins($search);

        $admins = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/admin/index.html.twig', [
            'admins' => $admins,
            'searchForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/admin/admins/create', name: 'app_admin_admin_create')]
    public function create(Request $request)
    {
        $admin = new User();

        $form = $this->createForm(RegistrationAdminType::class, $admin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($admin);

            $admin->setPassword(
                $form->has('plainPassword') ? $this->passwordHasher->hashPassword(
                    $admin,
                    $form->get('plainPassword')->getData()
                ) : ''
            );

            $admin->setCreatedAt(new DateTime());

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($admin, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Un compte admin a été crée');

            return $this->redirectToRoute('app_admin_admin_index');
        }

        return $this->render('admin/admin/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/admin/admins/{id}/edit', name: 'app_admin_admin_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, User $admin): Response
    {
        $form = $this->createForm(RegistrationAdminType::class, $admin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($admin);

            if ($form->get('plainPassword')->getData()) {
                $admin->setPassword(
                    $form->has('plainPassword') ? $this->passwordHasher->hashPassword(
                        $admin,
                        $form->get('plainPassword')->getData()
                    ) : ''
                );
            }

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Un compte admin a été mise à jour');

            return $this->redirectToRoute('app_admin_admin_index');
        }

        return $this->render('admin/admin/edit.html.twig', [
            'form' => $form->createView(),
            'admin' => $admin,
        ]);
    }

    #[Route(path: '/admin/admins/{id}/delete', name: 'app_admin_admin_delete', requirements: ['id' => '\d+'],  options: ['expose' => true])]
    public function delete(Request $request, User $admin)
    {
       $form = $this->deleteForm($admin);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($admin);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($admin);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Le compte admin a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le admin n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet compte ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $admin,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);

    }

    #[Route(path: '/admin/admins/bulk/delete', name: 'app_admin_admin_bulk_delete', options: ['expose' => true])]
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
                    $admin = $this->$this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($admin), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($admin, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les comptes admin ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les comptes admin n\'ont pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' comptes ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet compte ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_admin_delete', ['id' => $user->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_admin_bulk_delete'))
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


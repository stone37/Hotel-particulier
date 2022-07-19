<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Event\AdminCRUDEvent;
use App\Form\Filter\AdminUserType;
use App\Model\UserSearch;
use App\Repository\UserRepository;
use App\Service\UserBanService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private UserRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        UserRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    #[Route(path: '/admin/users', name: 'app_admin_user_index')]
    public function index(Request $request)
    {
        $search = new UserSearch();

        $form = $this->createForm(AdminUserType::class, $search);

        $form->handleRequest($request);

        $qb = $this->repository->getAdminUsers($search);

        $users = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'searchForm' => $form->createView(),
            'type' => 1,
        ]);
    }

    #[Route(path: '/admin/users/no-confirm', name: 'app_admin_user_no_confirm_index')]
    public function indexN(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator)
    {
        $search = new UserSearch();

        $form = $this->createForm(AdminUserType::class, $search);

        $form->handleRequest($request);

        $qb = $em->getRepository(User::class)->getUserNoConfirmed($search);

        $users = $paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'searchForm' => $form->createView(),
            'type' => 2,
        ]);
    }

    #[Route(path: '/admin/users/deleted', name: 'app_admin_user_deleted_index')]
    public function indexD(Request $request)
    {
        $search = new UserSearch();

        $form = $this->createForm(AdminUserType::class);

        $form->handleRequest($request);

        $qb = $this->repository->getUserDeleted($search);

        $users = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'searchForm' => $form->createView(),
            'type' => 3,
        ]);
    }

    #[Route(path: '/admin/users/{id}/show/{type}', name: 'app_admin_user_show', requirements: ['id' => '\d+', 'type' => '\d+'])]
    public function show(User $user, $type)
    {
       return $this->render('admin/user/show.html.twig', [
            'user' => $user,
            'type' => $type,
        ]);
    }

    #[Route(path: '/admin/users/{id}/ban', name: 'app_admin_user_ban', requirements: ['id' => '\d+'])]
    public function ban(Request $request, UserBanService $banService, User $user)
    {
        $banService->ban($user);
        $this->repository->flush();

        if ($request->isXmlHttpRequest()) {
            return $this->json([]);
        }

        $this->addFlash('success', "L'utilisateur a été banni");

        return $this->redirectToRoute('app_admin_user_index');
    }

    #[Route(path: '/admin/users/{id}/delete', name: 'app_admin_user_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, User $user)
    {
        $form = $this->deleteForm($user);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($user);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($user);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Le compte client a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le compte client n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet compte ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $user,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/admin/users/bulk/delete', name: 'app_admin_user_bulk_delete', options: ['expose' => true])]
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
                    $user = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($user), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($user, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les comptes clients ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les clients n\'ont pas pu être supprimée!');
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
            ->setAction($this->generateUrl('app_admin_user_delete', ['id' => $user->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_user_bulk_delete'))
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


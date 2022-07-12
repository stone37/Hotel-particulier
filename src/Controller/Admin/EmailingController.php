<?php

namespace App\Controller\Admin;

use App\Entity\Emailing;
use App\Entity\NewsletterData;
use App\Entity\User;
use App\Event\AdminCRUDEvent;
use App\Form\EmailingSenderType;
use App\Form\EmailingType;
use App\Repository\EmailingRepository;
use App\Service\NewsletterService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class EmailingController extends AbstractController
{
    private EmailingRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;
    private EntityManagerInterface $em;
    private NewsletterService $service;

    public function __construct(
        EmailingRepository $repository,
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher,
        NewsletterService $service)
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
        $this->service = $service;
    }

    #[Route(path: '/admin/emailing/{type}', name: 'app_admin_emailing_index')]
    public function index(Request $request, string $type)
    {
        $qb = $this->repository->getAdmin($type);

        $emailings = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/emailing/index.html.twig', [
            'emailings' => $emailings,
            'type' => $type,
        ]);
    }

    #[Route(path: '/admin/emailing/create', name: 'app_admin_emailing_create')]
    public function create(Request $request)
    {
        $emailing = new Emailing();

        $form = $this->createForm(EmailingSenderType::class, $emailing);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($emailing);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($emailing);

            $this->service->sendEmailing($emailing);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Votre email a été envoyé');

            return $this->redirectToRoute('app_admin_emailing_index', ['type' => '1']);
        }

        return $this->render('admin/emailing/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/admin/emailing/{id}/resend/{type}', name: 'app_admin_emailing_resend', requirements: ['id' => '\d+'])]
    public function resend(Request $request, Emailing $emailing, string $type)
    {
        if ($emailing->getGroupe() == Emailing::GROUP_PARTICULIER) {
            $form = $this->createForm(EmailingSenderType::class, $emailing);
        } else {
            $form = $this->createForm(EmailingType::class, $emailing);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($emailing->getGroupe() == Emailing::GROUP_PARTICULIER) {

                $this->repository->flush();

                $this->service->sendEmailing($emailing);
                $this->addFlash('success', 'Votre email a été envoyé');

                return $this->redirectToRoute('app_admin_emailing_index', ['type' => '1']);

            } elseif ($emailing->getGroupe() == Emailing::GROUP_USER) {
                $users = $this->em->getRepository(User::class)->findAllUsers();

                if (empty($users)) {
                    $this->addFlash('error', 'Votre newsletter n\'a pas pu etre envoyé par manque de destinataire');

                    return $this->redirectToRoute('app_admin_emailing_index', ['type' => '2']);
                }

                $this->repository->flush();
                $this->service->sendUserEmailing($emailing, $users);
                $this->addFlash('success', 'Votre newsletter a été envoyée avec succès');

                return $this->redirectToRoute('app_admin_emailing_index', ['type' => '2']);
            } else {
                $newsletters = $this->em->getRepository(NewsletterData::class)->findAll();

                if (empty($newsletters)) {
                    $this->addFlash('error', 'Votre newsletter n\'a pas pu etre envoyé par manque de destinataire');

                    return $this->redirectToRoute('app_admin_emailing_index', ['type' => '3']);
                }

                $this->repository->flush();

                $this->service->sendNewsletterEmailing($emailing, $newsletters);

                $this->addFlash('success', 'Votre newsletter a été envoyée avec succès');

                return $this->redirectToRoute('app_admin_emailing_index', ['type' => '3']);
            }
        }

        return $this->render('admin/emailing/edit.html.twig', [
            'form' => $form->createView(),
            'emailing' => $emailing,
            'type' => $type,
        ]);
    }

    #[Route(path: '/admin/emailing/create/user', name: 'app_admin_emailing_user')]
    public function user(Request $request): Response
    {
        $users = $this->em->getRepository(User::class)->findAllUsers();

        $emailing = (new Emailing())->setGroupe(Emailing::GROUP_USER);

        $form = $this->createForm(EmailingType::class, $emailing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (empty($users)) {
                $this->addFlash('error', 'Votre newsletter n\'a pas pu etre envoyé par manque de destinataire');

                return $this->redirectToRoute('app_admin_emailing_index', ['type' => '2']);
            }

            $this->repository->add($emailing);

            $this->service->sendUserEmailing($emailing, $users);

            $this->addFlash('success', 'Votre newsletter a été envoyée avec succès');

            return $this->redirectToRoute('app_admin_emailing_index', ['type' => '2']);
        }

        return $this->render('admin/emailing/user.html.twig', [
            'form' => $form->createView(),
            'users' => $users,
        ]);
    }

    #[Route(path: '/admin/emailing/create/newsletter', name: 'app_admin_emailing_newsletter')]
    public function newsletter(Request $request)
    {
        $newsletters = $this->em->getRepository(NewsletterData::class)->findAll();

        $emailing = (new Emailing())->setGroupe(Emailing::GROUP_NEWSLETTER);

        $form = $this->createForm(EmailingType::class, $emailing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (empty($newsletters)) {
                $this->addFlash('error', 'Votre newsletter n\'a pas pu etre envoyé par manque de destinataire');

                return $this->redirectToRoute('app_admin_emailing_index', ['type' => '3']);
            }

            $this->repository->add($emailing);

            $this->service->sendNewsletterEmailing($emailing, $newsletters);

            $this->addFlash('success', 'Votre newsletter a été envoyée avec succès');

            return $this->redirectToRoute('app_admin_emailing_index', ['type' => '3']);
        }

        return $this->render('admin/emailing/newsletter.html.twig', [
            'form' => $form->createView(),
            'newsletters' => $newsletters,
        ]);
    }

    #[Route(path: '/admin/emailing/newsletters', name: 'app_admin_emailing_newsletters')]
    public function newsletters(Request $request)
    {
        $qb = $this->em->getRepository(NewsletterData::class)->findBy([], ['createdAt' => 'desc']);

        $newsletters = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/emailing/newsletters.html.twig', [
            'newsletters' => $newsletters,
        ]);
    }

    #[Route(path: '/admin/emailing/{id}/delete', name: 'app_admin_emailing_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Emailing $emailing)
    {
        $form = $this->deleteForm($emailing);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($emailing);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($emailing);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Le mail a été supprimer !');
            } else {
                $this->addFlash('error', 'Désolé, le mail n\'a pas pu etre supprimer !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet mail ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $emailing,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/admin/emailing/bulk/delete', name: 'app_admin_emailing_bulk_delete', options: ['expose' => true])]
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
                    $emailing = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($emailing), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($emailing, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les mails ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les mails n\'ont pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' mails ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet mail ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Emailing $emailing)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_emailing_delete', ['id' => $emailing->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_emailing_bulk_delete'))
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



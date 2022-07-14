<?php

namespace App\Controller\Account;

use App\Controller\Traits\ControllerTrait;
use App\Entity\User;
use App\Service\DeleteAccountService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class AccountDeletionController extends AbstractController
{
    use ControllerTrait;

    private DeleteAccountService $service;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(DeleteAccountService $service, UserPasswordHasherInterface $passwordHasher)
    {
        $this->service = $service;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route(path: '/u/profil/delete', name: 'app_user_delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request): Response
    {
        $user = $this->getUserOrThrow();

        $form = $this->deleteForm();

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                if (!$this->passwordHasher->isPasswordValid($user, $data['password'] ?? '')) {
                    $this->addFlash('error', 'Impossible de supprimer le compte, mot de passe invalide');

                    return $this->redirectToRoute('app_user_delete');
                }

                $this->service->deleteUser($user, $request);

                $this->addFlash('error', 'Votre demande de suppression de compte a bien été prise en compte. 
            Votre compte sera supprimé automatiquement au bout de '.DeleteAccountService::DAYS.' jours');

                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('user/account/delete.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/u/profil/delete', name: 'app_user_delete_ajax', options: ['expose' => true])]
    #[IsGranted('ROLE_USER')]
    public function deleteAjax(Request $request): JsonResponse {

        /** @var User $user */
        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);

        if (!$this->isCsrfTokenValid('delete-account', $data['csrf'] ?? '')) {
            return new JsonResponse([
                'title' => 'Token CSRF invalide',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $data['password'] ?? '')) {
            return new JsonResponse([
                'title' => 'Impossible de supprimer le compte, mot de passe invalide',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $this->service->deleteUser($user, $request);

        return new JsonResponse([
            'message' => 'Votre demande de suppression de compte a bien été prise en compte. 
            Votre compte sera supprimé automatiquement au bout de '.DeleteAccountService::DAYS.' jours',
        ]);
    }

    #[Route(path: '/u/profil/cancel-delete', name: 'app_user_cancel_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function cancelDelete(EntityManagerInterface $em): RedirectResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->setDeleteAt(null);

        $em->flush();

        $this->addFlash('success', 'La suppression de votre compte a bien été annulée');

        return $this->redirectToRoute('app_user_delete');
    }

    private function deleteForm(): FormInterface
    {
        return $this->createFormBuilder([])
            ->setAction($this->generateUrl('app_user_delete'))
            ->setMethod('DELETE')
            ->add('password', PasswordType::class, [
                'attr' => ['placeholder' => 'Entrez votre mot de passer pour confirmer']
            ])
            ->getForm();
    }
}

<?php

namespace App\Controller;

use App\Auth\Authenticator;
use App\Entity\User;
use App\Event\UserCreatedEvent;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\SocialLoginService;
use App\Service\TokenGeneratorService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;


class RegistrationController extends AbstractController
{
    private EventDispatcherInterface $dispatcher;
    private UserPasswordHasherInterface $passwordHasher;
    private UserAuthenticatorInterface $userAuthenticator;
    private SocialLoginService $socialLoginService;
    private TokenGeneratorService $tokenGenerator;
    private Authenticator $authenticator;
    private UserRepository $repository;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        SocialLoginService $socialLoginService,
        TokenGeneratorService $tokenGenerator,
        EventDispatcherInterface $dispatcher,
        UserRepository $repository,
        Authenticator $authenticator)
    {
        $this->dispatcher = $dispatcher;
        $this->userAuthenticator = $userAuthenticator;
        $this->passwordHasher = $passwordHasher;
        $this->socialLoginService = $socialLoginService;
        $this->tokenGenerator = $tokenGenerator;
        $this->authenticator = $authenticator;
        $this->repository = $repository;
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        // Si l'utilisateur est connecté, on le redirige vers la home
        $loggedInUser = $this->getUser();

        if ($loggedInUser) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();

        $rootErrors = [];
        // Si l'utilisateur provient de l'oauth, on préremplit ses données
        $isOauthUser = $request->get('oauth') ? $this->socialLoginService->hydrate($user) : false;
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $form->has('plainPassword') ? $this->passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                ) : ''
            );

            $user->setCreatedAt(new DateTime());
            $user->setConfirmationToken($isOauthUser ? null : $this->tokenGenerator->generate(60));

            $this->repository->add($user);

            $this->dispatcher->dispatch(new UserCreatedEvent($user, $isOauthUser));

            if ($isOauthUser) {
                $this->addFlash('success', 'Votre compte a été créé avec succès');

                return $this->userAuthenticator->authenticateUser($user, $this->authenticator, $request)
                    ?: $this->redirectToRoute('app_dashboard_index');
            }

            $this->addFlash(
                'success',
                'Un message avec un lien de confirmation vous a été envoyé par mail. Veuillez suivre ce lien pour activer votre compte.'
            );

            return $this->redirectToRoute('app_login');
        } elseif ($form->isSubmitted()) {
            /** @var FormError $error */
            foreach ($form->getErrors() as $error) {
                if (null === $error->getCause()) {
                    $rootErrors[] = $error;
                }
            }
        }

        return $this->render('site/registration/register.html.twig', [
            'form' => $form->createView(),
            'errors' => $rootErrors,
            'menu' => 'register',
            'oauth_registration' => $request->get('oauth'),
            'oauth_type' => $this->socialLoginService->getOauthType(),
        ]);
    }

    #[Route(path: '/inscription/confirmation/{id}', name: 'app_register_confirm', requirements: ['id' => '\d+'])]
    public function confirmToken(User $user, Request $request): Response
    {
        $token = $request->get('token');

        if (empty($token) || $token !== $user->getConfirmationToken()) {
            $this->addFlash('error', "Ce token n'est pas valide");

            return $this->redirectToRoute('app_register');
        }

        if ($user->getCreatedAt() < new DateTime('-2 hours')) {
            $this->addFlash('error', 'Ce token a expiré');

            return $this->redirectToRoute('app_register');
        }

        $user->setConfirmationToken(null);
        $user->setIsVerified(true);
        $this->repository->flush();

        $this->addFlash('success', 'Votre compte a été validé.');

        return $this->redirectToRoute('app_login');
    }
}



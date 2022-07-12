<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Mailing\Mailer;
use App\Repository\BookingRepository;
use App\Repository\NewsletterDataRepository;
use App\Repository\PaymentRepository;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DashboardController extends AbstractController
{
    private UserRepository $userRepository;
    private PaymentRepository $paymentRepository;
    private BookingRepository $bookingRepository;
    private RoomRepository $roomRepository;
    private NewsletterDataRepository $newsletterDataRepository;

    public function __construct(
        UserRepository $userRepository,
        PaymentRepository $paymentRepository,
        BookingRepository $bookingRepository,
        RoomRepository $roomRepository,
        NewsletterDataRepository $newsletterDataRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->paymentRepository = $paymentRepository;
        $this->bookingRepository = $bookingRepository;
        $this->roomRepository = $roomRepository;
        $this->newsletterDataRepository = $newsletterDataRepository;
    }

    #[Route(path: '/admin', name: 'app_admin_dashboard')]
    public function index()
    {
        $taxe = $this->paymentRepository->totalTax();
        $reduction = $this->paymentRepository->totalReduction();
        $revenus = $this->paymentRepository->totalRevenues();

        $bookingConfirmNumber = $this->bookingRepository->getConfirmNumber();
        $bookingCancelNumber = $this->bookingRepository->getCancelNumber();
        $bookingArchiveNumber = $this->bookingRepository->getArchiveNumber();

        $today = new DateTime();
        $nextMonth = (new DateTime())->modify('+1 month');
        $roomTotal = $this->roomRepository->getRoomTotalNumber();
        $roomEnabledTotal = $this->roomRepository->getRoomEnabledTotalNumber();
        $roomBookingTotal = $this->bookingRepository->getRoomBookingTotalNumber($today, $nextMonth);

        //dump(new DateTime(), (new DateTime())->modify('+1 month'));
        //dump($roomBookingTotal);

        $bookingTotal = $bookingConfirmNumber+$bookingCancelNumber+$bookingArchiveNumber;
        $bookingCancelPercent = ($bookingTotal > 0) ? ($bookingCancelNumber * 100) / ($bookingTotal) : 0;

        return $this->render('admin/dashboard/index.html.twig', [
            'bookingNewNumber' => $this->bookingRepository->getNewNumber(),
            'bookingConfirmNumber' => $bookingConfirmNumber,
            'bookingCancelNumber' => $bookingCancelNumber,
            'bookingArchiveNumber' => $bookingArchiveNumber,
            'bookingCancelPercent' => round($bookingCancelPercent),
            'users' => $this->userRepository->getUserNumber(),
            'lastClients' => $this->userRepository->getLastClients(),
            'lastOrders' => $this->paymentRepository->getLasts(),
            'newsletterData' => $this->newsletterDataRepository->getNumber(),
            'months' => $this->paymentRepository->getMonthlyRevenues(),
            'days' => $this->paymentRepository->getDailyRevenues(),
            /*'taxMonths' => $this->paymentRepository->getMonthlyTaxRevenues(),
            'taxDays' => $this->paymentRepository->getDailyTaxRevenues(),*/
            'orders' => $this->paymentRepository->getNumber(),
            'revenus' => ($revenus - $taxe),
            'reduction' => $reduction,
            'roomTotal' => $roomTotal,
            'roomEnabledTotal' => $roomEnabledTotal,
            'roomBookingTotal' => $roomBookingTotal,
            'today' => $today,
            'nextMonth' => $nextMonth
        ]);
    }

    /**
     * Envoie un email de test à mail-tester pour vérifier la configuration du serveur.
     */
    #[Route(path: '/admin/mailtester', name: 'app_admin_mailtest', methods: ['POST'])]
    public function testMail(Request $request, Mailer $mailer): RedirectResponse
    {
        $email = $mailer->createEmail('mails/auth/register.twig', [
            'user' => $this->getUserOrThrow(),
        ])
            ->to($request->get('email'))
            ->subject('Hotel particulier | Confirmation du compte');
        $mailer->sendNow($email);

        $this->addFlash('success', "L'email de test a bien été envoyé");

        return $this->redirectToRoute('app_admin_dashboard');
    }

    private function getUserOrThrow(): User
    {
        $user = $this->getUser();

        if (!($user instanceof User)) {
            throw new AccessDeniedException();
        }

        return $user;
    }
}


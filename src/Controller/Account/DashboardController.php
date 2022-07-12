<?php

namespace App\Controller\Account;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Settings;
use App\Manager\SettingsManager;
use App\Repository\BookingRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    use ControllerTrait;

    private ?Settings $settings;
    private BookingRepository $bookingRepository;

    public function __construct(
        BookingRepository $bookingRepository,
        SettingsManager $manager)
    {
        $this->bookingRepository = $bookingRepository;
        $this->settings = $manager->get();
    }

    #[Route(path: '/u/', name: 'app_dashboard_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index()
    {
        $user = $this->getUserOrThrow();

        return $this->render('user/dashboard/index.html.twig', [
            'settings' => $this->settings,
            'user'     => $user,
            'booking' => $this->bookingRepository->getConfirmedByUserNumber($user),
        ]);
    }
}

<?php

namespace App\Controller\Admin;

use App\Exporter\MaintenanceConfigurationExporter;
use App\Factory\MaintenanceConfigurationFactory;
use App\Form\MaintenanceConfigurationType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MaintenanceController extends AbstractController
{
    private MaintenanceConfigurationExporter $maintenanceExporter;

    private MaintenanceConfigurationFactory $configurationFactory;

    public function __construct(
        MaintenanceConfigurationExporter $maintenanceExporter,
        MaintenanceConfigurationFactory $configurationFactory
    ) {
        $this->maintenanceExporter = $maintenanceExporter;
        $this->configurationFactory = $configurationFactory;
    }

    #[Route(path: '/admin/settings/maintenance', name: 'app_admin_maintenance_index')]
    public function index(Request $request): Response
    {
        $maintenanceConfiguration = $this->configurationFactory->get();

        $form = $this->createForm(MaintenanceConfigurationType::class, $maintenanceConfiguration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $maintenanceConfiguration->getEndDate() && $maintenanceConfiguration->getEndDate() < (new DateTime())) {
                $maintenanceConfiguration->setEnabled(false);
                $request->getSession()->getFlashBag()->add('error', 'La date de fin est dans le passé, la maintenance a été désactivée.');
            }

            $this->maintenanceExporter->export($maintenanceConfiguration);
            $message = 'La maintenance a été désactivé avec succès.';

            if ($maintenanceConfiguration->isEnabled()) {
                $message = 'La maintenance a été activé avec succès.';
            }

            $request->getSession()->getFlashBag()->add('success', $message);
        }

        return $this->render('admin/maintenance/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}



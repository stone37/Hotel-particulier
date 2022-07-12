<?php

namespace App\Subscriber;

use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use App\Factory\MaintenanceConfigurationFactory;
use App\Model\MaintenanceConfiguration;
use Twig\Environment;

final class MaintenanceSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;
    private Environment $twig;
    private MaintenanceConfigurationFactory $configurationFactory;

    public function __construct(
        Environment $twig,
        MaintenanceConfigurationFactory $configurationFactory,
        RequestStack $request
    ) {
        $this->twig = $twig;
        $this->configurationFactory = $configurationFactory;
        $this->requestStack = $request;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'handle',
        ];
    }

    public function handle(RequestEvent $event): void
    {
        $getRequestUri = $event->getRequest()->getRequestUri();

        /** @var string $adminPrefix */
        $adminPrefix = 'admin';

        $ipUser = $event->getRequest()->getClientIp();
        $maintenanceConfiguration = $this->configurationFactory->get();

        if (!$maintenanceConfiguration->isEnabled()) {
            return;
        }

        $authorizedIps = $maintenanceConfiguration->getArrayIpsAddresses();
        if (in_array($ipUser, $authorizedIps, true)) {
            return;
        }

        if (false === $this->isActuallyScheduledMaintenance($maintenanceConfiguration) &&
            (null !== $maintenanceConfiguration->getStartDate() ||
                null !== $maintenanceConfiguration->getEndDate())
        ) {
            return;
        }

        if (false !== mb_strpos($getRequestUri, $adminPrefix, 1)) {
            if ($this->requestStack->getMainRequest() === $this->requestStack->getCurrentRequest()) {
                $this->requestStack->getSession()->getFlashBag()->add('success', 'Le site est actuellement en maintenance.');
            }

            return;
        }

        $responseContent = 'Site en maintenance';

        if ('' !== $maintenanceConfiguration->getCustomMessage()) {
            $responseContent = $this->twig->render('Ui/_maintenance.html.twig', [
                'custom_message' => $maintenanceConfiguration->getCustomMessage(),
            ]);
        }

        $event->setResponse(new Response($responseContent, Response::HTTP_SERVICE_UNAVAILABLE));
    }

    private function isActuallyScheduledMaintenance(MaintenanceConfiguration $maintenanceConfiguration): bool
    {
        $now = new DateTime();
        $startDate = $maintenanceConfiguration->getStartDate();
        $endDate = $maintenanceConfiguration->getEndDate();
        // Now is between startDate and endDate
        if ($startDate !== null && $endDate !== null && ($now >= $startDate) && ($now <= $endDate)) {
            return true;
        }
        // No enddate provided, now is greater than startDate
        if ($startDate !== null && $endDate === null && ($now >= $startDate)) {
            return true;
        }
        // No startdate provided, now is before than enddate
        if ($endDate !== null && $startDate === null && ($now <= $endDate)) {
            return true;
        }
        // No schedule date
        return false;
    }
}



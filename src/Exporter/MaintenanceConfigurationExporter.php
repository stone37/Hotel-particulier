<?php

namespace App\Exporter;

use App\Manager\ConfigurationFileManager;
use App\Model\MaintenanceConfiguration;
use DateTimeInterface;

final class MaintenanceConfigurationExporter
{
    private ConfigurationFileManager $configurationFileManager;

    public function __construct(ConfigurationFileManager $configurationFileManager)
    {
        $this->configurationFileManager = $configurationFileManager;
    }

    public function export(MaintenanceConfiguration $configuration): void
    {
        $this->configurationFileManager->deleteMaintenanceFile();

        if (!$configuration->isEnabled()) {
            return;
        }

        $dataToExport = [];

        $ipAddresses = $configuration->getArrayIpsAddresses();

        if ([] !== $ipAddresses) {
            $dataToExport['ips'] = $ipAddresses;
        }

        $customMessage = $configuration->getCustomMessage();

        if ('' !== $customMessage) {
            $dataToExport['custom_message'] = $customMessage;
        }

        $scheduler = $this->getSchedulerArray($configuration->getStartDate(), $configuration->getEndDate());

        $this->configurationFileManager->createMaintenanceFile(array_merge(
            $scheduler,
            $dataToExport,
        ));
    }

    private function getSchedulerArray(?DateTimeInterface $startDate, ?DateTimeInterface $endDate): array
    {
        if (null === $startDate && null === $endDate) {
            return [];
        }

        $scheduler = ['scheduler' => []];

        if (null !== $startDate) {
            $scheduler['scheduler'] += ['start_date' => $startDate->format('Y-m-d H:i:s')];
        }
        if (null !== $endDate) {
            $scheduler['scheduler'] += ['end_date' => $endDate->format('Y-m-d H:i:s')];
        }
        if ([] === $scheduler['scheduler']) {
            return [];
        }

        return $scheduler;
    }
}



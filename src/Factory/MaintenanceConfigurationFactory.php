<?php

namespace App\Factory;

use App\Manager\ConfigurationFileManager;
use App\Model\MaintenanceConfiguration;

final class MaintenanceConfigurationFactory
{
    private ConfigurationFileManager $configurationFileManager;

    public function __construct(ConfigurationFileManager $configurationFileManager)
    {
        $this->configurationFileManager = $configurationFileManager;
    }

    public function get(): MaintenanceConfiguration
    {
        $maintenanceConfiguration = new MaintenanceConfiguration();

        if (!$this->configurationFileManager->hasMaintenanceFile()) {
            return $maintenanceConfiguration;
        }

        $maintenanceConfiguration->setEnabled(true);
        $maintenanceConfiguration = $maintenanceConfiguration->map($this->configurationFileManager->parseMaintenanceYaml());

        return $maintenanceConfiguration;
    }
}




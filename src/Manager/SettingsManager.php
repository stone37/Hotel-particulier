<?php

namespace App\Manager;

use App\Repository\SettingsRepository;

class SettingsManager
{
    private SettingsRepository $repository;

    public function __construct(SettingsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get()
    {
        return $this->repository->getSettings();
    }
}


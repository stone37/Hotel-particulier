<?php

namespace App\Service;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UploadService
{
    private ParameterBagInterface $parameter;

    public function __construct(ParameterBagInterface $parameter)
    {
        $this->parameter = $parameter;
    }

    public function getFilesUpload(SessionInterface $session): Finder
    {
        $finder = new Finder();

        try {
            $finder->in($this->getFindPath($session))->files();
        } catch (InvalidArgumentException $e) {
            $finder->append([]);
        }

        return $finder;
    }

    private function getFindPath(SessionInterface $session): string
    {
        return sprintf('%s/%s',
            $this->parameter->get('app.path.image_orphanage'),
            $this->getPath($session));
    }

    private function getPath(SessionInterface $session): string
    {
        return sprintf('%s/%s', $session->getId(), 'gallery');
    }

}
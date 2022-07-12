<?php

namespace App\Controller\Traits;

use InvalidArgumentException;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Trait UploadTrait
 * @package App\Controller\Traits
 */
Trait UploadTrait
{
    protected function getFiles(FileBag $bag): array
    {
        $files = [];
        $fileBag = $bag->all();
        $fileIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($fileBag), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($fileIterator as $file) {
            if (is_array($file) || null === $file) {
                continue;
            }

            $files[] = $file;
        }

        return $files;
    }

    protected function getFilesUpload(SessionInterface $session): Finder
    {
        $finder = new Finder();

        try {
            $finder->in($this->getFindPath($session))->files();
        } catch (InvalidArgumentException $e) {
            $finder->append([]);
        }

        return $finder;
    }

    protected function upload(File $file, SessionInterface $session)
    {
        if (!$session->isStarted()) {
            throw new RuntimeException('You need a running session in order to run the Orphanage.');
        }

        $path = sprintf('%s/%s', $this->getParameter('app.path.image_orphanage'), $this->getPath($session));
        $newFilename = uniqid().'.'.$file->guessExtension();

        $file->move($path, $newFilename);

        if (!$session->has('app_gallery_image'))
            $session->set('app_gallery_image', []);

        $data = $session->get('app_gallery_image');
        $data[] = [$newFilename => 0 ];
        $session->set('app_gallery_image', $data);
    }

    protected function getFindPath(SessionInterface $session): string
    {
        return sprintf('%s/%s',
            $this->getParameter('app.path.image_orphanage'),
            $this->getPath($session));
    }

    private function getPath(SessionInterface $session): string
    {
        return sprintf('%s/%s', $session->getId(), 'gallery');
    }
}


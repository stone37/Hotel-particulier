<?php

namespace App\Service;

use App\Entity\Gallery;
use App\Manager\OrphanageManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RequestStack;

class GalleryService
{
    private OrphanageManager $orphanageManager;
    private UploadService $uploadService;
    private RequestStack $request;
    private EntityManagerInterface $em;

    public function __construct(
        OrphanageManager $orphanageManager,
        UploadService $uploadService,
        EntityManagerInterface $em,
        RequestStack $request
    )
    {
        $this->orphanageManager = $orphanageManager;
        $this->uploadService = $uploadService;
        $this->request = $request;
        $this->em = $em;
    }

    public function add()
    {
        $files = $this->uploadService->getFilesUpload($this->request->getSession());

        if (empty($files)) {
            return false;
        }

        foreach ($files as $file) {
            $image = (new Gallery())
                ->setFile(new File($file->getPathname()));

            $this->em->persist($image);
        }

        $this->em->flush();

        return true;
    }

    public function initialize(Request $request)
    {
        if (!$request->isMethod('POST')) {
            $request->getSession()->set('app_gallery_image', []);
            $this->orphanageManager->initClear($request->getSession());
        }
    }
}


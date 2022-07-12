<?php

namespace App\Controller\Admin;

use App\Controller\Traits\ControllerTrait;
use App\Controller\Traits\UploadTrait;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class UploadController extends AbstractController
{
    use ControllerTrait;
    use UploadTrait;


    #[Route(path: '/admin/upload/image', name: 'app_image_upload_add', options: ['expose' => true])]
    public function add(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) $this->createNotFoundException('Mauvais requête');

        $files = $this->getFiles($request->files);

        foreach ($files as $file) {
            try {
                try {
                    $this->upload($file, $request->getSession());
                } catch (FileException) {}
            } catch (UploadException) {}
        }

        return new JsonResponse([]);
    }

    #[Route(path: '/upload/image/{pos}/delete', name: 'app_image_upload_delete', requirements: ['pos' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, $pos)
    {
        if (!$request->isXmlHttpRequest()) $this->createNotFoundException('Mauvais requête');
        if (!$request->getSession()->has('app_gallery_image')) return $this->createNotFoundException('Page introuvable');

        $data = $request->getSession()->get('app_gallery_image');

        $system = new Filesystem();
        $finder = new Finder();

        try {
            $finder->in($this->getFindPath($request->getSession()))->name(''.key($data[$pos]).'');
        } catch (InvalidArgumentException) {
            $finder->append([]);
        }

        foreach ($finder as $file) {
            $system->remove((string) $file->getRealPath());
            array_splice($data, $pos, 1);
            $request->getSession()->set('app_gallery_image', $data);
        }

        return new JsonResponse();
    }
}


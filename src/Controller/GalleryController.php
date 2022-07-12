<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Repository\GalleryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class GalleryController extends AbstractController
{
    use ControllerTrait;

    private GalleryRepository $repository;
    private Breadcrumbs $breadcrumbs;
    private PaginatorInterface $paginator;

    public function __construct(
        GalleryRepository $repository,
        Breadcrumbs $breadcrumbs,
        PaginatorInterface $paginator)
    {
        $this->repository = $repository;
        $this->breadcrumbs = $breadcrumbs;
        $this->paginator = $paginator;
    }

    #[Route(path: '/galleries', name: 'app_gallery_index')]
    public function index(): Response
    {
        $this->breadcrumb($this->breadcrumbs)->addItem('Notre gallérie');

        return $this->render('site/gallery/index.html.twig', [
            'galleries' => $this->repository->findBy([], ['position' => 'ASC'])
        ]);
    }
}



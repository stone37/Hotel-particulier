<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Repository\EquipmentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class ServiceController extends AbstractController
{
    use ControllerTrait;

    private Breadcrumbs $breadcrumbs;
    private PaginatorInterface $paginator;
    private EquipmentRepository $repository;

    public function __construct(
        Breadcrumbs $breadcrumbs,
        EquipmentRepository $repository,
        PaginatorInterface $paginator)
    {
        $this->breadcrumbs = $breadcrumbs;
        $this->repository = $repository;
        $this->paginator = $paginator;
    }

    #[Route(path: '/services', name: 'app_service_index')]
    public function index(): Response
    {
        $this->breadcrumb($this->breadcrumbs)->addItem('Nos services');

        return $this->render('site/service/index.html.twig', [
            'equipments' => $this->repository->findBy([], ['position' => 'ASC'], 15)
        ]);
    }
}

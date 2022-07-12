<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class PromotionController extends AbstractController
{
    use ControllerTrait;

    private EntityManagerInterface $em;
    private RoomRepository $repository;
    private Breadcrumbs $breadcrumbs;
    private PaginatorInterface $paginator;

    public function __construct(
        EntityManagerInterface $em,
        RoomRepository $repository,
        Breadcrumbs $breadcrumbs,
        PaginatorInterface $paginator)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->breadcrumbs = $breadcrumbs;
        $this->paginator = $paginator;
    }

    #[Route(path: '/nos-offres', name: 'app_promotion_index')]
    public function index(Request $request): Response
    {
        $this->breadcrumb($this->breadcrumbs)->addItem('Nos offres');

        $rooms = $this->paginator->paginate($this->repository->getEnabled(), $request->query->getInt('page', 1), 10);

        return $this->render('site/promotion/index.html.twig', [
            'rooms' => $rooms
        ]);
    }
}

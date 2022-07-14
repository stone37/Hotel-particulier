<?php

namespace App\Controller\Account;

use App\Controller\Traits\ControllerTrait;
use App\Repository\PaymentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
    use ControllerTrait;

    private PaymentRepository $paymentRepository;
    private PaginatorInterface $paginator;

    public function __construct(PaymentRepository $paymentRepository, PaginatorInterface $paginator)
    {
        $this->paymentRepository = $paymentRepository;
        $this->paginator = $paginator;
    }

    #[Route(path: '/u/invoices', name: 'app_dashboard_invoice_index')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request)
    {
        $user = $this->getUserOrThrow();

        $payments = $this->paymentRepository->findFor($user);
        $payments = $this->paginator->paginate($payments, $request->query->getInt('page', 1), 20);

        return $this->render('user/invoice/index.html.twig', [
            'user'     => $user,
            'payments'  => $payments,
        ]);
    }

    #[Route(path: '/u/invoices/{id}', name: 'app_dashboard_invoice_show', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function show(int $id)
    {
        $payment = $this->paymentRepository->findForId($id, $this->getUser());

        if (null === $payment) {
            throw new NotFoundHttpException();
        }

        return $this->render('user/invoice/show.html.twig', [
            'payment' => $payment,
        ]);
    }
}



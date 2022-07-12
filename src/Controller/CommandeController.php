<?php

namespace App\Controller;

use App\Data\BookingData;
use App\Event\PaymentEvent;
use App\Manager\OrderManager;
use App\Storage\CommandeSessionStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    private OrderManager $manager;
    private CommandeSessionStorage $storage;
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        OrderManager $manager,
        CommandeSessionStorage $storage,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->manager = $manager;
        $this->storage = $storage;
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    public function prepareCommande(BookingData $data)
    {

        /*$products = $this->em->getRepository(Product::class)
                        ->findArray(array_values($this->session->get('app_cart')));

        foreach ($products as $product) {
            $priceTTC = ((($product->getPrice() * $product->getTva()->getValue())/100)+$product->getPrice());

            $this->manager->addItem($product, $priceTTC);

            $totalHT += $product->getPrice();
            $totalTVA += $priceTTC - $product->getPrice();
        }*/


        //$this->manager->clearItems();
        //$this->manager->addItem($booking);

       /* $totalHT = ($data->discountAmount) ?
            $this->promotionPriceCalculator->calculate($data->amount, $data->discountAmount) : $data->amount;

        $totalTVA = $data->taxeAmount;*/

        /*$totalHT = ($booking->getRoomDiscount()) ?
            $this->promotionPriceCalculator->calculate($booking->getRoomPrice(), $booking->getRoomDiscount()) :
            $booking->getRoomPrice();
        $totalTVA = $booking->getTaxePrice();*/

        $commande = ($this->manager->getCurrent())
                ->setValidated(false)
                ->setReference(null)
                ->setAmount($data->amount)
                ->setTaxeAmount($data->taxeAmount)
                ->setDiscountAmount($data->discountAmount)
                ->setAmountTotal($data->amount+$data->taxeAmount-$data->discountAmount);


        if (!$this->storage->has()) {
            $this->em->persist($commande);
        }

        $this->em->flush();

        $this->storage->set($commande->getId());

        return new Response($commande->getId());
    }

    #[Route(path: '/commande/payment', name: 'app_commande_pay')]
    public function payment(Request $request)
    {
        $commande = $this->manager->getCurrent();

        if (!$commande || $commande->isValidated()) {
            throw $this->createNotFoundException('La commande n\'existe pas...');
        }

        if (!$request->getSession()->get('booking') instanceof BookingData) {
            throw $this->createNotFoundException('La commande n\'existe pas...');
        }

        $booking = $request->getSession()->get('booking');

        $this->dispatcher->dispatch(new PaymentEvent($booking, $commande));

        $request->getSession()->remove('orderId');
        $request->getSession()->remove('app_cart');
        $request->getSession()->remove('booking');

        return $this->redirectToRoute('app_commande_validated');


       /* $commande = $this->manager->getCurrent();

        if (!$commande || $commande->getValidated())
            throw $this->createNotFoundException('La commande n\'existe pas...');

        $commande->setValidated(true);
        $commande->setReference($this->generator->generate(10, false));
        $summary = $this->manager->summary();

        $payment = (new Payment())
            ->setCommande($commande)
            ->setPrice($summary->amountPaid())
            ->setDiscount($summary->getDiscount())
            ->setTaxe($summary->getTvaPriceTotal())
            ->setEnabled(true);

        $this->em->persist($payment);
        $this->em->flush();

        $this->dispatcher->dispatch(new PaymentEvent($payment, $commande));

        $this->session->remove('orderId');
        $this->session->remove('app_cart');
        $this->session->remove('app_advert');
        $this->session->remove('app_vignette');

        $this->addFlash('success', 'Felicitation, votre paiement a été effectué avec succès');

        return $this->redirectToRoute('app_home');*/
    }

    #[Route(path: '/commande/validate/success', name: 'app_commande_validated')]
    public function success()
    {
        return $this->render('site/commande/success.html.twig');
    }
}


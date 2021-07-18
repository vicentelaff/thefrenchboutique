<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountOrderController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    #[Route('/account/my-orders', name: 'account_orders')]
    public function index(): Response
    {
        $orders = $this->em->getRepository(Order::class)->findSuccessOrders($this->getUser());

        return $this->render('account/orders.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/account/my-orders/{reference}', name: 'account_show_order')]
    public function show($reference): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneBy(array('reference' => $reference));

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_show_order');
        }

        return $this->render('account/show_order.html.twig', [
            'order' => $order
        ]);
    }
}

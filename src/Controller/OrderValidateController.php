<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderValidateController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/order/success/{stripeSessionId}', name: 'order_validate')]
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneBy(array('stripeSessionId' => $stripeSessionId));

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($order->getState() == 0) {
            // Emptying the "cart" session:
            $cart->remove();

            // Change "isPaid" status to true:
            $order->setState(1);
            $this->em->flush();

            // Send a confirmation e-mail to the user:
            $mail = new Mail();
            $content = "Bonjour " . $order->getUser()->getFirstname() . "<br/>Thank you for your order at The French Boutique.<br/>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse sollicitudin condimentum sapien, non iaculis metus accumsan et. Aliquam nec justo in metus venenatis porta. Fusce mollis lorem id nisi sodales accumsan. Suspendisse massa dui, dapibus a est a, malesuada pulvinar magna. Pellentesque in velit augue. Integer aliquet venenatis faucibus. Nunc vitae feugiat enim, quis fermentum ipsum.";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Your order has been confirmed and is ready to be taken care of by our team.', $content);
        }

        return $this->render('order_validate/index.html.twig', [
            'order' => $order
        ]);
    }
}

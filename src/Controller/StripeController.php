<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Order;
use App\Entity\Products;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class StripeController extends AbstractController
{
    #[Route('/order/create-session/{reference}', name: 'stripe_create_session')]
    public function index(EntityManagerInterface $em, Cart $cart, $reference): Response
    {
        Stripe::setApiKey('sk_test_51JE72YCY6525tlrfsbUjb48B3gMNDO635KnWZiIQaYKhGSYHJysA5VwuWduxYkync8O3IFD9asX5nxICLPHpgXA500LED2mnXj');

        // header('Content-Type: application/json');
        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://localhost:8000';

        $order = $em->getRepository(Order::class)->findOneBy(array('reference' => $reference));

        if (!$order) {
            new JsonResponse(['error' => 'order']);
        }

        foreach ($order->getOrderDetails()->getValues() as $product) {
            $product_object = $em->getRepository(Products::class)->findOneBy(array('name' => $product->getProduct()));

            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN . "/uploads/" . $product_object->getImage()],
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];
        }


        // Shipping options:
        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
            ],
            'quantity' => 1,
        ];


        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                $products_for_stripe
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/order/success/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/order/error/{CHECKOUT_SESSION_ID}',
        ]);


        $order->setStripeSessionId($checkout_session->id);
        $em->flush();


        // header("HTTP/1.1 303 See Other");
        // header("Location: " . $checkout_session->url);

        $response = new JsonResponse(['id' => $checkout_session->id]);
        return $response;
    }
}

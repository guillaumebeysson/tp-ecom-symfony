<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Session\Cart;
use Stripe\Checkout\Session;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{   

    /**
     * @Route("/create-checkout-session.php/{reference}", name="stripe_checkout")
     */
    public function index($reference, OrderRepository $repo, EntityManagerInterface $em): Response
    {

        $order = $repo->findOneByReference($reference);

        /**Si pas de reference */
        if(!$order){
            return new JsonResponse([ 'error' => 'order' ]);
        }

        $YOUR_DOMAIN = 'http://localhost:8000';
        $product_stripe = [];


        Stripe::setApiKey('sk_test_51J2EBeHaByFbRYAbzr0YEDjy4iEbuk8BJoDlTHhZndBD5nlmudwdzl9sO5FIoK3Vo4MBJ8r8wtvgESNuAxCPm2Ui00EwpJ9Gqi');

        foreach ($order->getOrderDetails()->getvalues() as $product) {
            // j'importe OrderDetails
            $product_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => ['https://via.placeholder.com/150'],
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];
        }

        $product_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getDelivererPrice() * 100,
                'product_data' => [
                    'name' => $order->getDeliverer(),
                    'images' => ['https://via.placeholder.com/150'],
                ],
            ],
            'quantity' => 1,
        ];

        $checkout_session = Session::create([

            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [$product_stripe],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/order/success/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/order/cancel/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);

        $em->persist($order);

        $em->flush();

        return new JsonResponse(['id' => $checkout_session->id]);
    }
}

<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Session\Cart;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{   

    /**
     * @Route("/create-checkout-session.php", name="stripe_checkout")
     */
    public function index(Cart $cart): Response
    {

        Stripe::setApiKey('sk_test_51J2EBeHaByFbRYAbzr0YEDjy4iEbuk8BJoDlTHhZndBD5nlmudwdzl9sO5FIoK3Vo4MBJ8r8wtvgESNuAxCPm2Ui00EwpJ9Gqi');

        $YOUR_DOMAIN = 'http://localhost:8000';

        $productStripe = [];

        foreach ($cart->getFullInfoProduct() as $product) {
            $productStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product['product']->getPrice(),
                    'product_data' => [
                      'name' => $product['product']->getName(),
                      'images' => $product['product']->getIllustration(),
                    ],
                ],
                'quantity' => $product['quantity'],
            ];
        }

        $checkout_session = Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [$productStripe],
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/success.html',
        'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);

        return new JsonResponse(['id' => $checkout_session->id]);
    }
}

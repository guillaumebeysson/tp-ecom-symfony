<?php

namespace App\Controller;

use App\Entity\Order;
use App\Session\Cart;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="order")
     */
    public function index(Request $request, Cart $cart, ProductRepository $repo): Response
    {
        //Je crée une variable 'fullInfoProduct'
        $fullInfoProduct = [];
        foreach ($cart->get() as $id => $quantity) {
            $product = $repo->findOneById($id);

            //si aucun produit n'existe, continu
            if(!$product){
                continue;
            }

            $fullInfoProduct[] = [ 
                'product' => $product,
                'quantity' => $quantity,
            ];
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser(), 
            ]);

        // $form = $this->createForm(OrderType::class);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $fullInfoProduct,
        ]);
    }

    /**
     * @Route("/order/recap", name="order_recap", methods="POST")
     */
    public function recap(Request $request, Cart $cart, ProductRepository $repo, EntityManagerInterface $em): Response
    {
        //Je crée une variable 'fullInfoProduct'
        $fullInfoProduct = [];
        foreach ($cart->get() as $id => $quantity) {
            $product = $repo->findOneById($id);

            //si aucun produit n'existe, continu
            if(!$product){
                continue;
            }

            $fullInfoProduct[] = [ 
                'product' => $product,
                'quantity' => $quantity,
            ];
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser(), 
            ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $date = new \DateTime();

            /*Je crée une référence unique */
            $reference = $date->format('dmY').'-'. uniqid();

            $deliverer = $form->get("carrier")->getData();
            $delivery = $form->get("address")->getData();

            $addressDelivery = $delivery->getFirstName().'  '.$delivery->getLastName();

            if ($delivery->getCompany()){
                $addressDelivery .= '<br>' . $delivery->getCompany();
            }

            $addressDelivery .= '<br>' . $delivery->getPhone();
            $addressDelivery .= '<br>' . $delivery->getaddress();
            $addressDelivery .= '<br>' . $delivery->getPostalCode();
            $addressDelivery .= ' , ' . $delivery->getCity();
            $addressDelivery .= ' - ' . $delivery->getCountry();

            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setDeliverer($deliverer->getName());
            $order->setDelivererPrice($deliverer->getPrice());
            $order->setDeliveryAddress($addressDelivery);
            $order->setIsPaid(0);
            $order->setReference($reference);

            $em->persist($order);

            
            //Je crée une variable 'fullInfoProduct'
            $fullInfoProduct = [];
            foreach ($cart->get() as $id => $quantity) {
                $product = $repo->findOneById($id);

                //si aucun produit n'existe, continu
                if(!$product){
                    continue;
                }

                $fullInfoProduct[] = [ 
                    'product' => $product,
                    'quantity' => $quantity,
                ];
            }

            
            
            foreach ($fullInfoProduct as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);

                $em->persist($orderDetails);
            }

            $em->flush();
            
            return $this->render('order/recap.html.twig', [
                'cart' => $fullInfoProduct,
                'deliverer' => $deliverer,
                'delivery' => $addressDelivery,
                'reference' =>$reference,
            ]);
        }

        // $form = $this->createForm(OrderType::class);
        return $this->redirectToRoute('cart');

    }

    /**
     * @Route("/order/success/{stripeId}", name="order_success")
     */

     public function success($stripeId, OrderRepository $repo, EntityManagerInterface $em){

        $order = $repo->findOneByStripeSessionId($stripeId);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        /**Si ma commande n'est pas payée alors je peux */
        if(!$order->getIsPaid()){
            // changer son status et passer en "Payé"
            $order->setIsPaid(1);
            $em->persist($order);
            // et j'enregistre en BDD
            $em->flush();
        }

         return $this->render("order/success.html.twig", [
             'order' => $order,
         ]);
     }


      /**
     * @Route("/order/cancel/{stripeId}", name="order_cancel")
     */

    public function cancel($stripeId, OrderRepository $repo){

        $order = $repo->findOneByStripeSessionId($stripeId);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }


         return $this->render("order/cancel.html.twig", [
             'order' => $order,
         ]);
    }
}

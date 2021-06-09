<?php

namespace App\Controller;

use App\Session\Cart;
use App\Form\OrderType;
use App\Repository\ProductRepository;
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
     * @Route("/order/recap", name="order_recap")
     */
    public function recap(Request $request, Cart $cart, ProductRepository $repo): Response
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



        }

        // $form = $this->createForm(OrderType::class);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $fullInfoProduct,
        ]);
    }
}

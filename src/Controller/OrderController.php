<?php

namespace App\Controller;

use App\Form\OrderType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="order")
     */
    public function index(Request $request): Response
    {

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser(), 
            ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

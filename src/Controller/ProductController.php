<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/products", name="product")
     */
    public function index(ProductRepository $repo): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $repo->findAll(),
        ]);
    }

     /**
     * @Route("/product/{slug}", name="product_show")
     */
    public function show($slug, ProductRepository $repo): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $repo->findOneBy(['slug' => $slug]),
        ]);
    }
}

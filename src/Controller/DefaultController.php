<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index()
    {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        return $this->render('default/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/{id}", name="show_details", methods={"GET"})
     * @param Product $product
     * @return Response
     */
    public function show(Product $product): Response
    {
        return $this->render('default/show.html.twig', [
            'product' => $product,
        ]);
    }
}

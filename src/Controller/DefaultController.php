<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/product", name="all_product")
     */
    public function all()
    {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        return $this->render('default/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/product/{id}", name="show_details", methods={"GET"})
     * @param Product $product
     * @return Response
     */
    public function show(Product $product): Response
    {
        return $this->render('default/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/search", name="search", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function search(Request $request)
    {
        if ($request->request->has('key'))
            $text = $request->request->get('key');

        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->getByText($text);

        return $this->render('default/result.html.twig', [
            'products' => $products,
            'key' => $text,
        ]);
    }
}

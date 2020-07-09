<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\TypeFlowerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/admin/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/no", name="product_table", methods={"GET"})
     */
    public function index(): Response
    {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @param Request $request
     * @param int $page
     * @param string $_format
     * @param ProductRepository $products
     * @param TypeFlowerRepository $types
     * @return Response
     * @throws \Exception
     * @Route("/{id}", name="product_table", methods={"GET"})
     */
    public function indexPaginated(Request $request, int $page, string $_format, ProductRepository $products, TypeFlowerRepository $types): Response
    {
        $type = null;
        if ($request->query->has('type_flower'))
            $type = $types->findOneBy(['name' => $request->query->get('type_flower')]);

        $latestProduct = $products->findLatest($page, $type);

        // Every template name also has two extensions that specify the format and
        // engine for that template.
        // See https://symfony.com/doc/current/templates.html#template-naming
        return $this->render('product/index.'.$_format.'.twig', [
            'paginator' => $latestProduct,
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('urlImg')->getData();

            if ($imageFile)
                $product->setUrlImg($this->uploadFile($slugger, $imageFile));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_table');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="product_show", methods={"GET"})
     * @param Product $product
     * @return Response
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Product $product
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function edit(Request $request, Product $product, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('urlImg')->getData();

            if ($imageFile !== null) {
                $lastUrlImg = $product->getUrlImg();
                $product->setUrlImg($this->uploadFile($slugger, $imageFile));
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_table');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_table');
    }

    /**
     * @param SluggerInterface $slugger
     * @param $imageFile
     * @return string|null
     */
    public function uploadFile(SluggerInterface $slugger, $imageFile)
    {
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFile = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

        try {
            $imageFile->move(
                $this->getParameter('images_directory'),
                $newFile
            );
        } catch (FileException $e) {

        }
        return $newFile;
    }

    /**
     *
     */
    public function deleteFile()
    {

    }
}

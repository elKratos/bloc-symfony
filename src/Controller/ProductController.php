<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\TypeFlower;
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
     * @Route("/", name="product_table", methods={"GET"})
     */
    public function index(): Response
    {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'url' => $this->getUrl(),
            'types_flower' => $this->allTypesFlower(),
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
            'types_flower' => $this->allTypesFlower(),
            'url' => $this->getUrl(),
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
            'url' => $this->getUrl(),
            'types_flower' => $this->allTypesFlower(),
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
            'url' => $this->getUrl(),
            'types_flower' => $this->allTypesFlower(),
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
            'url' => $this->getUrl(),
            'types_flower' => $this->allTypesFlower(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="product_delete", methods={"DELETE"})
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
     * @Route("/search", name="search_product", methods={"POST"})
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

        return $this->render('product/result.html.twig', [
            'products' => $products,
            'filter' => $text,
            'url' => $this->getUrl(),
            'types_flower' => $this->allTypesFlower(),
        ]);
    }

    /**
     * @Route("/date", name="date_product", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function date(Request $request)
    {
        if ($request->request->has('start'))
            $start = $request->request->get('start');

        if ($request->request->has('end'))
            $end = $request->request->get('end');

        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->getByDate($start, $end);

        return $this->render('product/result.html.twig', [
            'products' => $products,
            'filter' => $start." & ".$end,
            'url' => $this->getUrl(),
            'types_flower' => $this->allTypesFlower(),
        ]);
    }

    /**
     * @Route("/type-filter", name="type_filter_product", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function getByType(Request $request)
    {
        if ($request->request->has('type'))
            $type = $request->request->get('type');

        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->getByType($type);

        $type = $this->getDoctrine()
            ->getRepository(TypeFlower::class)
            ->findOneBy(['id' => $type])
            ->getName();

        return $this->render('product/result.html.twig', [
            'products' => $products,
            'filter' => $type,
            'url' => $this->getUrl(),
            'types_flower' => $this->allTypesFlower(),
        ]);
    }

    /**
     * @return string
     */
    public function getUrl():string
    {
        return '/admin/product/';
    }

    /**
     * @return array
     */
    public function allTypesFlower():array
    {
        return $this->getDoctrine()
                    ->getRepository(TypeFlower::class)
                    ->findAll();
    }
}

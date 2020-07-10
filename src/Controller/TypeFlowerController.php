<?php

namespace App\Controller;

use App\Entity\TypeFlower;
use App\Form\TypeFlowerType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/type/flower")
 */
class TypeFlowerController extends AbstractController
{
    /**
     * @Route("/", name="type_flower_table", methods={"GET"})
     */
    public function index(): Response
    {
        $typeFlowers = $this->getDoctrine()
            ->getRepository(TypeFlower::class)
            ->findAll();

        return $this->render('type_flower/index.html.twig', [
            'type_flowers' => $typeFlowers,
        ]);
    }

    /**
     * @Route("/new", name="type_flower_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $typeFlower = new TypeFlower();
        $form = $this->createForm(TypeFlowerType::class, $typeFlower);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($typeFlower);
            $entityManager->flush();

            return $this->redirectToRoute('type_flower_table');
        }

        return $this->render('type_flower/new.html.twig', [
            'type_flower' => $typeFlower,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="type_flower_show", methods={"GET"})
     * @param TypeFlower $typeFlower
     * @return Response
     */
    public function show(TypeFlower $typeFlower): Response
    {
        return $this->render('type_flower/show.html.twig', [
            'type_flower' => $typeFlower,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="type_flower_edit", methods={"GET","POST"})
     * @param Request $request
     * @param TypeFlower $typeFlower
     * @return Response
     */
    public function edit(Request $request, TypeFlower $typeFlower): Response
    {
        $form = $this->createForm(TypeFlowerType::class, $typeFlower);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('type_flower_table');
        }

        return $this->render('type_flower/edit.html.twig', [
            'type_flower' => $typeFlower,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="type_flower_delete", methods={"DELETE"})
     * @param Request $request
     * @param TypeFlower $typeFlower
     * @return Response
     */
    public function delete(Request $request, TypeFlower $typeFlower): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeFlower->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($typeFlower);
            $entityManager->flush();
        }

        return $this->redirectToRoute('type_flower_table');
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

        $typeFlowers = $this->getDoctrine()
            ->getRepository(TypeFlower::class)
            ->getByText($text);

        return $this->render('type_flower/result.html.twig', [
            'types_flowers' => $typeFlowers,
            'filter' => $text,
        ]);
    }
}

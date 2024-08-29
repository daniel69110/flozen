<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{

    #[Route('/admin/interface', name: 'app_admin_interface')]
    public function index(): Response
    {
        return $this->render('admin_interface/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/interface/product/{id?}', name: 'product')]
    public function product(Request $request, EntityManagerInterface $entityManager, int $id = null): Response
    {
        $product = $id ? $entityManager->getRepository(Product::class)->find($id) : new Product();

        if (!$product) {
            throw $this->createNotFoundException('Le produit demandé n\'existe pas.');
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', [
                'title' => 'Top!',
                'message' => 'Votre annonce a bien été ajoutée !'
            ]);

            return $this->redirectToRoute('product', ['id' => $product->getId()]);
        }

        return $this->render('admin_interface/product.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    #[Route('/admin/interface/product/{id}/delete', name: 'product_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Le produit demandé n\'existe pas.');
        }

        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('success', [
                'title' => 'Supprimé!',
                'message' => 'Le produit a bien été supprimé.'
            ]);
        }

        return $this->redirectToRoute('listing');
    }

    #[Route('/admin/interface/product/{id}/edit', name: 'product_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Le produit demandé n\'existe pas.');
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', [
                'title' => 'Mis à jour!',
                'message' => 'Le produit a bien été mis à jour.'
            ]);

            return $this->redirectToRoute('product', ['id' => $product->getId()]);
        }

        return $this->render('admin_interface/product.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    #[Route('/admin/interface/product/{id}', name: 'product_show')]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Le produit demandé n\'existe pas.');
        }

        return $this->render('admin_interface/product_show.html.twig', [
            'product' => $product,
        ]);
    }
    // #[Route('/admin/interface/product', name: 'product')]
    // public function product(Request $request , EntityManagerInterface $entityManager, Product $product): Response
    // {
    //     $form = $this->createForm(ProductType::class, $product);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {

    //         $entityManager->persist($product);
    //         $entityManager->flush();

    //         $this->addFlash('success', [
    //             'title' => 'Top!',
    //             'message' => 'Votre annonce a bien été ajoutée !'
    //         ]);
    //         return $this->redirectToRoute('product');
    //     }

    //     return $this->render('admin_interface/product.html.twig', [
    //         'form' => $form,
    //     ]);
    // }
}

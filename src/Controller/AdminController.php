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

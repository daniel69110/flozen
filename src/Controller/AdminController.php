<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class AdminController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/product/{id?}', name: 'product')]
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

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/product/{id}/delete', name: 'product_delete', methods: ['POST'])]
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

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/product/{id}/edit', name: 'product_edit')]
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


    #[Route('/admin/product/{id}/show', name: 'product_show')]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Le produit demandé n\'existe pas.');
        }

        return $this->render('listing/product_show.html.twig', [
            'product' => $product,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/reservations', name: 'admin_reservation')]
    public function adminReservations(EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur connecté est un admin
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette page.');
        }

        // Récupérer toutes les réservations
        $reservations = $entityManager->getRepository(Booking::class)->findAll();

        // Afficher les réservations dans la vue admin
        return $this->render('admin_interface/reservation.html.twig', [
            'reservations' => $reservations,

        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/reservation/{id}/confirm', name: 'admin_confirm_reservation')]
    public function confirmReservation(Booking $reservation, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur connecté est un admin
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette action.');
        }

        // Vérifier si la réservation est en attente
        if ($reservation->getStatus() === 'En attente') {
            $reservation->setStatus('Confirmer');  // Mettre à jour le statut
            $entityManager->flush();  // Sauvegarder les changements
        }

        return $this->redirectToRoute('admin_reservation');  // Rediriger vers la page des réservations
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/reservation/{id}/cancel', name: 'admin_cancel_reservation')]
    public function cancelReservation(Booking $reservation, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur connecté est un admin
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette action.');
        }

        // Vérifier si la réservation est en attente
        if ($reservation->getStatus() === 'En attente') {
            $reservation->setStatus('Annuler');  // Mettre à jour le statut
            $entityManager->flush();  // Sauvegarder les changements
        }

        return $this->redirectToRoute('admin_reservation');  // Rediriger vers la page des réservations
    }
}

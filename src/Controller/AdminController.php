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

            $this->addFlash('success', 'Votre annonce a bien été ajoutée !');

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


    #[Route('/user/product/{id}/show', name: 'product_show')]
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


    #[Route('/admin/reservation/{id}/confirm', name: 'admin_confirm_reservation', methods: ['POST'])]
    public function adminConfirmBooking(Booking $booking, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est un administrateur
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Mettre à jour le statut de la réservation à "Confirmé"
        $booking->setStatus('Confirmé');
        $entityManager->flush();

        $this->addFlash('success', 'La réservation a été confirmée avec succès.');

        // Rediriger vers la liste des réservations ou une autre page pour l'admin
        return $this->redirectToRoute('admin_reservation');
    }


    #[Route('/admin/reservation/{id}/cancel', name: 'admin_cancel_reservation', methods: ['POST'])]
    public function adminCancelBooking(Booking $booking, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est un administrateur
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Mettre à jour le statut de la réservation à "Annulé"
        $booking->setStatus('Annulé');
        $entityManager->flush();

        $this->addFlash('success', 'La réservation a été annulée avec succès.');

        // Rediriger vers la liste des réservations ou une autre page pour l'admin
        return $this->redirectToRoute('admin_reservation');
    }
}

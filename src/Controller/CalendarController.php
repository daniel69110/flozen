<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CalendarController extends AbstractController
{
    #[Route('/calendar', name: 'calendar')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);

        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // Récupérer l'utilisateur connecté
                $user = $this->getUser();

                // Assigner l'utilisateur à la réservation
                $booking->setUser($user);
                $booking->setStatus('En attente'); // Assigne un statut par défaut, ici 'pending'

                $entityManager->persist($booking);
                $entityManager->flush();

                return $this->redirectToRoute('home');
            }
        } else {
            // Afficher un message d'erreur si l'utilisateur n'est pas connecté
            $this->addFlash('error', 'Veuillez vous connecter pour réserver un rendez-vous.');
        }


        return $this->render('calendar/calendar.html.twig', [
            'controller_name' => 'CalendarController',
            'form' => $form->createView(), // Passer le formulaire à la vue
        ]);
    }
}

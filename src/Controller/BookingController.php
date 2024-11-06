<?php

namespace App\Controller;

use App\Entity\Availability;
use App\Entity\Booking;
use App\Form\BookingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    #[Route('/booking/{id}/create', name: 'booking_create')]
    public function createBooking(Request $request, Availability $availability, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Créer une nouvelle réservation
        $booking = new Booking();
        $booking->setAvailability($availability);
        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setUser($user);
            $availability->setAvailable(false);

            $entityManager->persist($booking);
            $entityManager->flush();


            return $this->redirectToRoute('home');
        }



        // Rediriger ou afficher un message de succès
        return $this->render('booking/index.html.twig', [
            'form' => $form,
            'startDateTime' => $availability->getStartDateTime(),
            'endDateTime' => $availability->getEndDateTime()
        ]); // redirige vers la liste des réservations
    }
    
}

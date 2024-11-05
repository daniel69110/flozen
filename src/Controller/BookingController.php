<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    #[Route('/booking/create', name: 'booking_create')]
    public function createBooking(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les données du formulaire
        $startDateTime = new \DateTime($request->request->get('startDateTime'));
        $endDateTime = new \DateTime($request->request->get('endDateTime'));
        $name = $request->request->get('name');
    
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
    
        // Créer une nouvelle réservation
        $booking = new Booking();
        $booking->setStartDateTime($startDateTime);
        $booking->setEndDateTime($endDateTime);
        $booking->setStatus('confirmed'); // ou le statut que vous souhaitez
        $booking->setName($name);
        $booking->setUser($user);
    
        // Enregistrer la réservation en base de données
        $entityManager->persist($booking);
        $entityManager->flush();
    
        // Rediriger ou afficher un message de succès
        return $this->redirectToRoute('appointment'); // redirige vers la liste des réservations
    }
    
}


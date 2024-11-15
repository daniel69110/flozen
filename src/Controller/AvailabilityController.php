<?php

namespace App\Controller;

use App\Entity\Availability;
use App\Form\AvailabilityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AvailabilityController extends AbstractController
{
    #[Route('/admin/availability', name: 'availability')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $availability = new Availability();
        $form = $this->createForm(AvailabilityType::class, $availability);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($availability);
            $entityManager->flush();

            $this->addFlash('success', 'Ajoutée avec succès.');

            return $this->redirectToRoute('availability');
        }

        return $this->render('availability/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/availability/list', name: 'availability_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $availabilities = $entityManager->getRepository(Availability::class)->findAll();

        return $this->render('availability/list.html.twig', [
            'availabilities' => $availabilities,
        ]);
    }

    #[Route('/admin/availability/delete/{id}', name: 'availability_delete')]
    public function delete(Availability $availability, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($availability);
        $entityManager->flush();

        return $this->redirectToRoute('availability_list');
    }
}

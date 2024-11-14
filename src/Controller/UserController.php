<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\EmailType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class UserController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/user/home', name: 'user_home')]
    public function index(): Response
    {
        return $this->render('user_interface/base.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/user/account', name: 'account')]
    public function account(Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(EmailType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->flush();

            $this->addFlash('success',  'Votre profil a été mis à jour avec succès.');
            return $this->redirectToRoute('account');
        }

        return $this->render('user_interface/account.html.twig', [
            'user' => $form,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/user/password', name: 'password')]
    public function password(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {

        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifiez que l'ancien mot de passe est correct
            $oldPassword = $form->get('oldPassword')->getData();
            if (!$userPasswordHasher->isPasswordValid($user, $oldPassword)) {

                return $this->redirectToRoute('password');
            }

            // Hash le nouveau mot de passe et le définissez pour l'utilisateur
            $newPassword = $form->get('newPassword')->getData();
            if ($newPassword) {
                $hashedPassword = $userPasswordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', [
                    'title' => 'Top!',
                    'message' => 'Votre mot de passe a été mis à jour avec succès.'
                ]);


                return $this->redirectToRoute('password');
            }
        }
        return $this->render('user_interface/password.html.twig', [
            'passwordForm' => $form,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/user/delete', name: 'delete_account', methods: ['POST', 'GET'])]
    public function deleteAccount(Request $request, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
    {
        // Récupérer l'utilisateur connecté
        /** @var User $user */
        $user = $this->getUser();

        // Vérifier que l'utilisateur a confirmé sa suppression (par exemple via un token CSRF ou une confirmation spécifique)
        if ($this->isCsrfTokenValid('delete-account', $request->get('_token'))) {
            // Déconnecter l'utilisateur avant la suppression
            $tokenStorage->setToken(null); // Utilisation de TokenStorageInterface

            // Supprimer l'utilisateur de la base de données
            $entityManager->remove($user);
            $entityManager->flush();

            // Redirection après la suppression
            return $this->redirectToRoute('app_login'); // Ou vers la page d'accueil
        }

        // Si le token CSRF n'est pas valide ou si une autre condition échoue
        $this->addFlash('error', [
            'title' => 'Erreur',
            'message' => 'Une erreur est survenue lors de la suppression de votre compte.',
        ]);

        return $this->redirectToRoute('home');
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/user/infos', name: 'infos')]
    public function infos(SessionInterface $session, UserRepository $userRepository): Response
    {
        // Récupérez l'utilisateur connecté
        /** @var User $user */
        $user = $this->getUser();

        $users = $userRepository->findAll();

        $totalUser = 0;

        foreach ($users as $user) {
            if (in_array('ROLE_USER', $user->getRoles())) {
                $totalUser++;
            }
        }

        $session->set('totalUser', $totalUser);


        return $this->render('user_interface/infos.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user, // Passez l'utilisateur à la vue
            'totalUser' => $session->get('totalUser'),
        ]);
    }

    #[Route('/user/appointment', name: 'appointment')]
    public function listReservations(EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Récupérer les réservations de cet utilisateur
        $appointments = $entityManager->getRepository(Booking::class)->findBy(['user' => $user]);

        return $this->render('user_interface/appointment.html.twig', [
            'appointments' => $appointments,  // Passer les rendez-vous à la vue
        ]);
    }

    #[Route('/appointment/delete/{id}', name: 'appointment_delete', methods: ['POST'])]
    public function deleteReservation(int $id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Trouver la réservation par son identifiant
        $appointment = $entityManager->getRepository(Booking::class)->find($id);

        // Vérifier si la réservation existe et appartient à l'utilisateur
        if ($appointment && $appointment->getUser() === $user) {
            // Supprimer la réservation
            $entityManager->remove($appointment);
            $entityManager->flush();

            $this->addFlash('success', 'Réservation supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Réservation non trouvée ou vous n\'avez pas la permission de la supprimer.');
        }

        // Rediriger vers la liste des réservations
        return $this->redirectToRoute('appointment');
    }

    #[Route('/user/appointment/{id}/cancel', name: 'appointment_cancel')]
    public function cancelBooking(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur connecté est bien le propriétaire de la réservation
        if ($booking->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez annuler que vos propres réservations.');
        }

        // Mettre à jour le statut de la réservation à "annulé" (ou supprimer si nécessaire)
        $booking->setStatus('Annuler');
        $entityManager->flush();

        // Rediriger vers la page des rendez-vous
        return $this->redirectToRoute('appointment');
    }
}

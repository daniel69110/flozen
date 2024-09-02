<?php

namespace App\Controller;

use App\Entity\ProfilUser;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\EmailType;
use App\Form\ProfilUserType;
use App\Form\PasswordType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


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
        // Si l'utilisateur a un profil existant, le formulaire sera pré-rempli avec les informations existantes, sinon un formulaire vierge sera affiché 
        // $profilUser = $this->getUser()->getProfilUser() ?? new ProfilUser();

        $form = $this->createForm(EmailType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $profilUser->setUser($this->getUser());
            // encode the plain password

            // $entityManager->persist($profilUser);
            $entityManager->flush();

            // do anything else you need here, like send an email
            $this->addFlash('success', [
                'title' => 'Top!',
                'message' => 'Votre profil a été mis à jour avec succès.'
            ]);
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
    #[Route('/user/appointment', name: 'appointment')]
    public function appointment(): Response
    {
        return $this->render('user_interface/appointment.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/user/infos', name: 'infos')]
    public function infos(): Response
    {
        // Récupérez l'utilisateur connecté
        /** @var User $user */
        $user = $this->getUser();
            
        return $this->render('user_interface/infos.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user, // Passez l'utilisateur à la vue
        ]);
    }
    // {
    //     return $this->render('user_interface/infos.html.twig', [
    //         'controller_name' => 'UserController',
    //     ]);
    // }
}

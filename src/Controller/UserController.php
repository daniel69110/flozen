<?php

namespace App\Controller;

use App\Entity\ProfilUser;
use App\Form\ProfilUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/user/home', name: 'user_home')]
    public function index(): Response
    {
        return $this->render('user_interface/base.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/account', name: 'account')]
    public function account(Request $request,EntityManagerInterface $entityManager): Response
    {
        $profilUser = new ProfilUser();
        $form = $this->createForm(ProfilUserType::class, $profilUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password

            $entityManager->persist($profilUser);
            $entityManager->flush();

            // do anything else you need here, like send an email

            
        }

        return $this->render('user_interface/account.html.twig', [
            'profilUser' => $form,
        ]);
    }

    #[Route('/user/password', name: 'password')]
    public function password(): Response
    {
        return $this->render('user_interface/password.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/appointment', name: 'appointment')]
    public function appointment(): Response
    {
        return $this->render('user_interface/appointment.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/infos', name: 'infos')]
    public function infos(): Response
    {
        return $this->render('user_interface/infos.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}

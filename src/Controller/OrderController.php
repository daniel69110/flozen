<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
class OrderController extends AbstractController
{
    #[Route('/user/user_orders', name: 'user_orders')]
    public function listUserOrders(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer toutes les commandes de l'utilisateur connecté
        $orders = $entityManager->getRepository(Order::class)->findBy(['user' => $user]);

        return $this->render('order/list.html.twig', [
            'orders' => $orders, // On passe la liste des commandes au template
        ]);
    }
}

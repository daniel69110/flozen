<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Statut;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
#[Route('/webhook', name: 'app_webhook_')]
class WebhookController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;

    public function __construct(EntityManagerInterface $entityManager, ProductRepository $productRepository)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
    }

    #[Route('/stripe', name: 'stripe')]
    public function stripeWebhook(Request $request): Response
    {
        $payload = (string)$request->getContent();
        $signature = $request->headers->get('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                $_ENV['STRIPE_WEBHOOK_KEY']
            );
        } catch (\UnexpectedValueException $e) {
            return new JsonResponse(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new JsonResponse(['error' => 'Invalid signature'], 400);
        }

        // Gestion de l'événement checkout.session.completed
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // Récupérer les informations de la commande (ex. ID utilisateur, total)
            $userId = $session->client_reference_id;
            $totalAmount = $session->amount_total / 100; // Convertir en euros

            // Créer et hydrater l'entité Order
            $order = new Order();
            $order->setDateOrder(new \DateTime());
            $order->setCreatedAt(new \DateTimeImmutable());
            $order->setTotal($totalAmount);
            $order->setUser($this->getUser()); // Remplace par la logique de récupération de l'utilisateur

            // Associer le statut à la commande
            $statut = $this->entityManager->getRepository(Statut::class)->findOneBy(['name' => 'Paid']);
            if ($statut) {
                $order->setStatut($statut);
            }

            // Ajouter les lignes de commande à partir des produits du panier
            foreach ($session->display_items as $item) {
                $product = $this->productRepository->find($item->custom_id);
                if ($product) {
                    $orderLine = new OrderLine();
                    $orderLine->setOrder($order);
                    $orderLine->setProduct($product);
                    $orderLine->setQuantity($item->quantity);
                    $this->entityManager->persist($orderLine);
                }
            }

            // Sauvegarder la commande et ses lignes
            $this->entityManager->persist($order);
            $this->entityManager->flush();
        }

        return new JsonResponse(['status' => 'success'], 200);
    }
}

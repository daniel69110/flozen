<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/webhook', name: 'app_webhook_')]
class WebhookController extends AbstractController
{
    #[Route('/stripe', name: 'stripe')]
    public function stripeWebhook(
        Request $request,
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository
    ): Response {
        $payload = (string)$request->getContent();
        $signature = $request->headers->get('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                $_ENV['STRIPE_WEBHOOK_KEY']
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return new JsonResponse('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return new JsonResponse('Invalid signature', 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object; // Stripe Checkout Session

                // Récupérer l'ID de la commande depuis les metadata
                $orderId = $session->metadata->order_id ?? null;

                if ($orderId) {
                    // Rechercher la commande dans la base de données
                    $order = $entityManager->getRepository(Order::class)->find($orderId);

                    if ($order) {
                        // Récupérer les informations des articles depuis les metadata ou une autre source
                        $cartItems = json_decode($session->metadata->cart_items, true); // Les items sont encodés en JSON

                        // Hydrater les lignes de commande (OrderLine)
                        foreach ($cartItems as $item) {
                            $product = $productRepository->find($item['product_id']);
                            if ($product) {
                                $orderLine = new OrderLine();
                                $orderLine->setOrder($order);
                                $orderLine->setProduct($product);
                                $orderLine->setQuantity($item['quantity']);
                                $orderLine->setPrice($item['price']); // Utiliser le prix de l'article

                                $entityManager->persist($orderLine);
                            }
                        }

                        // Mettre à jour la commande avec des valeurs supplémentaires, si nécessaire
                        $order->setDateOrder(new \DateTime()); // Par exemple, mettre à jour la date de la commande
                        $order->setTotal($session->amount_total / 100); // Montant total en euros

                        // Enregistrer les modifications dans la base de données
                        $entityManager->persist($order);
                        $entityManager->flush();
                    } else {
                        // Si la commande n'est pas trouvée
                        return new JsonResponse('Order not found', 404);
                    }
                } else {
                    // Si l'ID de la commande n'est pas présent dans les metadata
                    return new JsonResponse('Order ID not found in metadata', 400);
                }

                break;
            default:
                return new JsonResponse('Received unknown event type ' . $event->type, 400);
        }

        return new JsonResponse(['status' => 'success'], 200);
    }
}
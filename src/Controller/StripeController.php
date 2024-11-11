<?php

namespace App\Controller;

use App\Service\Cart\Cart;
use App\Service\Cart\CartItem;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StripeController extends AbstractController
{
    #[Route('/checkout', name: 'checkout')]
    public function checkout(Cart $cart): Response
    {
        // Pour être sûr d'utiliser la dernière version de l'API de Stripe et surtout ne jamais en changer
        // automatiquement si une nouvelle version venait à sortir, nous spécifions la version que nous souhaitons utiliser.
        $stripe = new StripeClient([
            'api_key' => $_ENV['STRIPE_API_KEY'],
            'stripe_version' => '2024-06-20'
        ]);

        $lineItems = [];
        /** @var CartItem $cartItem */
        foreach ($cart->getItems() as $cartItem) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'EUR',
                    'product_data' => [
                        'name' => $cartItem->getProduct()->getName(),
                    ],
                    'unit_amount_decimal' => $cartItem->getProduct()->getPrice() * 100,
                ],
                'quantity' => $cartItem->getQuantity()
            ];
        }

        // N'oubliez pas de changer les URLs de succès et d'échec avec l'URL que Ngrok va vous fournir pour créer une passerelle.
        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            // 'success_url' => 'https://127.0.0.1:8000/checkout/success',
            'success_url' => 'https://rniyy-2a01-cb14-c15-e800-c003-fb26-3307-f54a.a.free.pinggy.link/checkout/success',
            'line_items' => $lineItems
        ]);

        return $this->redirect($session->url);
    }

    #[Route('/checkout/success', name: 'checkout_success')]
    public function checkoutSuccess(Cart $cart): Response
    {
        return $this->render('checkout/success.html.twig', [
            'cart' => $cart
        ]);
    }
}
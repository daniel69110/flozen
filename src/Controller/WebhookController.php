<?php

namespace App\Controller;

use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/webhook', name: 'app_webhook_')]
class WebhookController extends AbstractController
{
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
            // Invalid payload
            http_response_code(400);
            echo json_encode(['Error parsing payload: ' => $e->getMessage()]);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            echo json_encode(['Error verifying webhook signature: ' => $e->getMessage()]);
            exit();
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                
                break;
            default:
                echo 'Received unknown event type ' . $event->type;
        }
        return $this->json(['status' => 'success'],200);
    }
}

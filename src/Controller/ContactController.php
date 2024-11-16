<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactDTO();


        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            $mail = (new Email())
                ->from('user2@mail.com')
                ->to('user1@mail.com')
                ->subject('Demande de contact')
                ->html($this->renderView('emails/contact.html.twig', ['data' => $data]));

            $mailer->send($mail);

            $this->addFlash('success', 'Votre email a bien été envoyé');
            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/conditions', name: 'terms_conditions')]
    public function termsConditions(): Response
    {
        return $this->render('legal/terms_conditions.html.twig');
    }

    #[Route('/politique-de-confidentialite', name: 'privacy_policy')]
    public function privacyPolicy(): Response
    {
        return $this->render('legal/privacy_policy.html.twig');
    }

    #[Route('/mentions-legales', name: 'legal_mentions')]
    public function legalMentions(): Response
    {
        return $this->render('legal/legal_mentions.html.twig');
    }
}

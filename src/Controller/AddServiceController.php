<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddServiceController extends AbstractController
{
    #[Route('/service', name: 'app_add_service')]
    public function index(): Response
    {
        return $this->render('add_service/index.html.twig', [
            'controller_name' => 'AddServiceController',
        ]);
    }
}

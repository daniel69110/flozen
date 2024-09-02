<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route; 

class ServiceController extends AbstractController
{

   #[Route(path:'/service',name:'service')]
   public function service(): Response{

      return $this->render('service.html.twig');
   }

   
}
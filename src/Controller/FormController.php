<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route; 

class FormController extends AbstractController
{

   #[Route(path:'/form',name:'form')]
   public function form(): Response{

      return $this->render('form.html.twig');
   }
}
<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use App\Form\ProductQuantityType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ListingController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/listing', name: 'listing')]
    public function list(): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        $forms = [];
        foreach ($products as $product){
            $forms[$product->getId()] = $this->createForm(ProductQuantityType::class, null, [
                'action' => $this->generateUrl('add_to_cart', ['id' => $product->getId()]),
                'method' => 'POST'
            ])->createView();
        }
        return $this->render('listing/index.html.twig', [
            'products' => $products,
            'forms' => $forms
        ]);
    }

   
}

<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;

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

        return $this->render('listing/show.html.twig', [
            'products' => $products,
        ]);
    }
}

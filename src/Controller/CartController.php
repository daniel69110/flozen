<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductQuantityType;
use App\Repository\ProductRepository;
use App\Service\Cart\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/add-to-cart/{id}', name: 'add_to_cart')]
    public function addToCart(Product $product, Request $request, SessionInterface $session, Cart $cart): Response
    {
        $form = $this->createForm(ProductQuantityType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $quantity = $form->get('quantity')->getData();
            $cart->addProduct($product, $quantity);
        }

        return $this->redirectToRoute('listing');
    }

    #[Route('/cart', name: 'cart')]
    public function show(SessionInterface $session, ProductRepository $productRepository, Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }


    #[Route('/remove/{id}', name: 'remove')]
    public function removeFromCart(SessionInterface $session, int $id): Response
    {
        // Récupérer le panier depuis la session
        $cart = $session->get('cart', []);
// Vérifier si le produit existe dans le panier
        if (isset($cart[$id])) {
            unset($cart[$id]);
// Supprimer le produit du panier
            $session->set('cart', $cart);
// Mettre à jour le panier dans la session
        }

        // Rediriger vers la page du panier
        return $this->redirectToRoute('cart');
    }
}

// namespace App\Controller;

// use App\Entity\Product;
// use App\Form\ProductQuantityType;
// use App\Repository\ProductRepository;
// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\HttpFoundation\Session\SessionInterface;
// use Symfony\Component\Routing\Attribute\Route;

// class CartController extends AbstractController
// {
//     #[Route('/add-to-cart/{id}', name: 'add_to_cart')]
//     public function addToCart(Product $product, Request $request, SessionInterface $session): Response
//     {

//         $form = $this->createForm(ProductQuantityType::class);
//         $form->handleRequest($request);
//         if ($form->isSubmitted() && $form->isValid()) {

//             $quantity = $form->get('quantity')->getData();
//             $cart = $session->get('cart', []);

//             if (empty($cart[$product->getId()])) {
//                 $cart[$product->getId()] = $quantity;
//             } else {
//                 $cart[$product->getId()] += $quantity;
//             }

//             $session->set('cart', $cart);
//         }

//         return $this->redirectToRoute('listing');
//     }

//     #[Route('/cart','cart')]
//     public function show(SessionInterface $session, ProductRepository $productRepository): Response
//     {
//         $cart = $session->get('cart',[]);
//         $products = [];
//         $total = 0;
//         foreach ($cart as $id => $quantity){
//             $product = $productRepository->find($id);
//             $products[] = $product;
//             $total += $product->getPrice() * $quantity;

//         }

//         return $this->render('cart/index.html.twig',[
//             'products' => $products,
//             'totalPrice' => $total

//         ]);

//     }

//     #[Route('/remove/{id}', name: 'remove')]
//     public function removeFromCart(SessionInterface $session, int $id): Response
//     {
//         // Récupérer le panier depuis la session
//         $cart = $session->get('cart', []);

//         // Vérifier si le produit existe dans le panier
//         if (isset($cart[$id])) {
//             unset($cart[$id]);  // Supprimer le produit du panier
//             $session->set('cart', $cart);  // Mettre à jour le panier dans la session
//         }

//         // Rediriger vers la page du panier
//         return $this->redirectToRoute('cart');
//     }
// }

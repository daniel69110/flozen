<?php

namespace App\Service\Cart;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private SessionInterface $session;
    private ArrayCollection $items;

    public function __construct(
        RequestStack $requestStack,
        ProductRepository $productRepository
    ) {
        $this->session = $requestStack->getSession();
        $cartData = $this->session->get('cart', []);

        // Initialiser $this->items comme une ArrayCollection d'objets CartItem
        $this->items = new ArrayCollection();

        foreach ($cartData as $productId => $quantity) {
            // Récupérer le produit via le ProductRepository
            $product = $productRepository->find($productId);

            // Créer un CartItem et l'ajouter à la collection
            if ($product) {
                $this->items->add(new CartItem($product, $quantity));
            }
        }
    }

    public function addProduct(Product $product, int $quantity = 1): void {
        $carItem = $this->items->filter(function (CartItem $item) use ($product) {
            return $item->getProduct()->getId() === $product->getId();
        })->first();

        if ($carItem) {
            $carItem->incrementQuantity($quantity);
        } else {
            $this->items->add(new CartItem($product, $quantity));
        }
        $this->save();
    }

    public function removeProduct(Product $product): void {
        $carItem = $this->items->filter(function (CartItem $item) use ($product) {
            return $item->getProduct()->getId() === $product->getId();
        })->first();

        if ($carItem) {
            $this->items->removeElement($carItem);
        }
        $this->save();
    }

    public function getItems(): array {
        return $this->items->toArray();
    }

    public function clear(): void {
        $this->items->clear();
        $this->save();
    }

    public function getTotalItems(): int {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getQuantity();
        }
        return $total;
    }

    public function getTotalPrice(): float {
        $totalPrice = 0;
        foreach ($this->items as $item) {
            $totalPrice += $item->getProduct()->getPrice() * $item->getQuantity();
        }
        return $totalPrice;
    }

    private function save(): void {
        $cartData = [];

        foreach ($this->items as $item) {
            $cartData[$item->getProduct()->getId()] = $item->getQuantity();
        }

        $this->session->set('cart', $cartData);
    }
}
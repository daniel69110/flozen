<?php

namespace App\Service\Cart;

use App\Entity\Product;

class CartItem
{
    private Product $product;
    private int $quantity;

    public function __construct(Product $product, int $quantity = 1)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function incrementQuantity(int $quantity = 1): void
    {
        $this->quantity += $quantity;
    }

    public function decrementQuantity(int $quantity = 1): void
    {
        $this->quantity = max(0, $this->quantity - $quantity);
    }
}

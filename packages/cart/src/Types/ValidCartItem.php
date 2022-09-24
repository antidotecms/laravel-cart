<?php

namespace Antidote\LaravelCart\Types;

use Antidote\LaravelCart\DataTransferObjects\CartItem;
use InvalidArgumentException;

class ValidCartItem
{
    private CartItem $cartItem;

    /**
     * @param CartItem $cartItem
     * @throws InvalidArgumentException
     */
    private function __construct(CartItem $cartItem) {
        $this->cartItem = $cartItem;
        $this->isValid() ?: throw new InvalidArgumentException('The cart item is invalid');
    }

    /**
     * @param CartItem $cartItem
     * @throws InvalidArgumentException
     * @return CartItem
     */
    public static function create(CartItem $cartItem) : CartItem {
        $cartItem->quantity > 0 ?: throw new \InvalidArgumentException('Quantity must be greater than or equal to one');
        return (new self($cartItem))->getCartItem();
    }

    private function getCartItem() : CartItem {
        return $this->cartItem;
    }

    /**
     * @return bool
     */
    private function isValid() : bool {
        $this->cartItem->getProduct()->productType ?: throw new InvalidArgumentException('Product has no product data type associated');

        return $this->cartItem->getProduct()->checkValidity($this->cartItem->product_data);
    }
}

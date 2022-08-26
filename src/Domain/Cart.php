<?php

namespace Antidote\LaravelCart\Domain;

use Antidote\LaravelCart\Models\CartAdjustment;
use Illuminate\Support\Collection;

/**
 * @method
 */
class Cart
{
    public function __call($method, $arguments) : mixed
    {
        method_exists($this, $method) ?: throw new \BadMethodCallException();

        $allowedMethods = [
            'add',
            'items',
            'clear',
            'getDiscountTotal',
            'getSubtotal',
            'getTotal',
            'isInCart',
            'remove'
        ];

        in_array($method, $allowedMethods) ?: throw new \BadMethodCallException();

        return $this->$method(...$arguments);
    }

    private function items() : Collection
    {
        $cart_items = session()->get('cart_items') ?? [];
        $cart = new \Antidote\LaravelCart\DataTransferObjects\Cart(cart_items: $cart_items);

        return $cart->cart_items ?? collect([]);
    }

    private function add($product, int $quantity = 1, $product_data = null)
    {
        //ensure the product has a product type
        if(!$product->productDataType)
        {
            throw new \Exception('Product has no product data type associated');
        }

        $cart_items = $this->items();

        $items = $cart_items
            ->where('product_id', '=', $product->id);

        if(!$items->count()) {

            $cart_items->push([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'product_data' => $product_data
            ]);

        }
        else
        {
            $cart_items->each(function($cart_item) use ($product, $quantity, $product_data) {

                if($cart_item->getProduct()->product_data_type_type == get_class($product->productDataType) &&
                    $cart_item->product_id == $product->id &&
                    $cart_item->product_data == $product_data) {
                        $cart_item->quantity = $cart_item->quantity + $quantity;
                }

            });
        }

        $cart_items = $cart_items->toArray();

        session()->put('cart_items', $cart_items);
    }

    /**
     * @param $product mixed the product
     * @param $quantity int of products to remove. If null, all specified products are removed.
     * @return void
     */
    private function remove($product, $quantity = null, $product_data = null) : void
    {
        $cart_items = $this->items();

            $cart_items->transform(function($cart_item) use ($product, $quantity, $product_data) {

                if ($cart_item->product_id == $product->id && !$product_data)
                {
                    $cart_item->quantity -= ($quantity ?? $cart_item->quantity);
                }
                elseif ($cart_item->product_id == $product->id && $product_data && $cart_item->product_data == $product_data)
                {
                    $cart_item->quantity -= ($quantity ?? $cart_item->quantity);
                }

                return $cart_item;
            });

            $cart_items = $cart_items->reject(function($cart_item) {
                 return $cart_item->quantity == 0;
            });

        session()->put('cart_items', $cart_items->toArray());
    }

    /**
     * Clears the contents of the cart
     * @return void
     */
    private function clear()
    {
        session()->put('cart_items', []);
    }

    private function getSubtotal()
    {
        $subtotal = 0;

        $this->items()->each(function($cart_item) use (&$subtotal)
        {
            $subtotal += $cart_item->getProduct()->getPrice($cart_item->product_data ?? null) * $cart_item->quantity;
        });

        return $subtotal;
    }

    private function getTotal()
    {
        $total = $this->getSubtotal();

        $total -= $this->getDiscountTotal();

        return $total;
    }

    private function getDiscountTotal()
    {
        $discount_total = 0;

        CartAdjustment::all()->each(function($adjustment) use (&$discount_total)
        {
            $discount_total += $adjustment->isActive() && $adjustment->isValid() ? $adjustment->amount() : 0;
        });

        return $discount_total;
    }

    private function isInCart($product_id) : bool
    {
        return $this->items()
            ->contains(function($cart_item) use ($product_id) {
                return $cart_item->product_id == $product_id;
            });
    }
}

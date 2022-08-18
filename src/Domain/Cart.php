<?php

namespace Antidote\LaravelCart\Domain;

use Antidote\LaravelCart\Contracts\Product;
use Antidote\LaravelCart\Contracts\Shopper;
use Antidote\LaravelCart\Contracts\VariableProduct;
use Antidote\LaravelCart\Models\CartAdjustment;
use Illuminate\Support\Collection;
use PHPUnit\Framework\MockObject\InvalidMethodNameException;
use function PHPUnit\Framework\throwException;

class Cart
{
    public function __call($method, $arguments) : mixed
    {
        if(!method_exists($this, $method)) {
            throw new \BadMethodCallException();
            return null;
        }

        //@todo throw invalid method exception if method doesnt exist
        return auth()->user() && class_implements(auth()->user(), Shopper::class) ?
            auth()->user()->$method(...$arguments) :
            $this->$method(...$arguments);
    }

    private function cartitems() : Collection
    {
        $cart_items = session()->get('cart_items') ?? [];
        $cart = new \Antidote\LaravelCart\DataTransferObjects\Cart(cart_items: $cart_items);
        return $cart->cart_items;
    }

    private function add(Product | VariableProduct $product, int $quantity = 1, $specification = null)
    {
        $cart_items = $this->cartitems();

        $items = $cart_items
            ->where('product_id', '=', $product->id)
            ->where('product_type', '=', get_class($product))
            ->where('specification', '=', $specification);

        if(!$items->count()) {

                $cart_items->push([
                    'product_id' => $product->id,
                    'product_type' => get_class($product),
                    'specification' => $specification,
                    'quantity' => $quantity
                ]);
        }
        else
        {
            $cart_items = $cart_items->each(function($cart_item) use ($product, $quantity, $specification) {

                if($cart_item->product_type == get_class($product) &&
                    $cart_item->product_id == $product->id &&
                    $cart_item->specification == $specification) {
                    $cart_item->quantity = $cart_item->quantity + $quantity;
                }

            });

        }

        $cart_items = $cart_items->toArray();

        session()->put('cart_items', $cart_items);
    }

    /**
     * @param $product_id id of the product
     * @param $quantity number of products to remove. If null, all specified products are removed.
     * @return void
     */
    private function remove($product_id, $quantity = null) : void
    {
        $cart_items = $this->cartitems();

        if(!$quantity)
        {
            $this->removeAll($product_id);
        }
        else
        {
            $cart_items = $cart_items->each(function($cart_item) use ($product_id, $quantity) {
                if($cart_item->product_id == $product_id)
                {
                    $cart_item->quantity = $cart_item->quantity - $quantity;
                }
            });

            session()->put('cart_items', $cart_items->toArray());
        }
    }

    private function removeAll($product_id)
    {
        $cart_items = $this->cartitems();
        //$cart_items->where('product_id', $product_id)->delete();
        $cart_items = $this->cartitems()->reject(function($cart_item) use ($product_id) {
            return $cart_item->product_id == $product_id;
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

        //$cart_items = $this->cartitems();

        $this->cartitems()->each(function($cart_item) use (&$subtotal)
        {
            if(is_a($cart_item->getProduct(), VariableProduct::class))
            {
                $subtotal += $cart_item->getProduct()->getPrice($cart_item->specification) * $cart_item->quantity;
            }
            else
            {
                $subtotal += $cart_item->getProduct()->getPrice() * $cart_item->quantity;
            }
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

    private function isInCart($product_classname, $product_id) : bool
    {
        return $this->cartitems()
            ->contains(function($cart_item) use ($product_id, $product_classname) {
                return $cart_item->product_id == $product_id &&
                    $cart_item->product_type == $product_classname;
            });
//            ->where('product_type', $product_classname)
//            ->where('product_id', $product_id)
//            ->exists();
    }
}

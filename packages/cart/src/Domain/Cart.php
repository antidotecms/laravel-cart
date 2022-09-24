<?php

namespace Antidote\LaravelCart\Domain;

use Antidote\LaravelCart\Contracts\Customer;
use Antidote\LaravelCart\Contracts\Order;
use Antidote\LaravelCart\DataTransferObjects\CartItem;
use Antidote\LaravelCart\Models\CartAdjustment;
use Antidote\LaravelCart\Types\ValidCartItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
            'remove',
            'createOrder'
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

    private function add($product, int $quantity = 1, $product_data = null) : void
    {
        $cart_item = ValidCartItem::create(new CartItem([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'product_data' => $product_data
        ]));

        $cart_items = $this->items();

        $items = $cart_items
            ->where('product_id', '=', $product->id);

        if(!$items->count()) {
            $cart_items->push($cart_item);
        }
        else
        {

            $cart_items->filter(function($cart_item) use ($product, $quantity, $product_data) {
                    return $cart_item->getProduct()->product_type_type == get_class($product->productType) &&
                        $cart_item->product_id == $product->id &&
                        $cart_item->product_data == $product_data;
                })
                ->whenNotEmpty(function($collection) use ($quantity, $cart_items, $product_data) {
                    return $collection->map(function($cart_item) use ($quantity, $product_data) {
                        //if($cart_item->product_data == $product_data) {
                            $cart_item->quantity +=  $quantity;
                        //}
                    });
                })
                ->whenEmpty(function($collection) use ($product, $quantity, $product_data, $cart_items) {

                    return $cart_items->push([
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'product_data' => $product_data
                    ]);
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
                 return $cart_item->quantity <= 0;
            });

        session()->put('cart_items', $cart_items->toArray());
    }

    /**
     * Clears the contents of the cart
     * @return void
     */
    private function clear() : void
    {
        session()->put('cart_items', []);
    }

    private function getSubtotal() : int
    {
        $subtotal = 0;

        $this->items()->each(function($cart_item) use (&$subtotal)
        {
            $subtotal += $cart_item->getCost();
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

        $this->getValidDiscounts()->each(function(CartAdjustment $adjustment) use (&$discount_total) {
            $discount_total += $adjustment->amount();
        });

        return $discount_total;
    }

    private function getValidDiscounts() : Collection
    {
        return CartAdjustment::all()->filter(function(CartAdjustment $adjustment) {
            return $adjustment->isActive() && $adjustment->isValid();
        });
    }

    private function isInCart($product_id) : bool
    {
        return $this->items()
            ->contains(function($cart_item) use ($product_id) {
                return $cart_item->product_id == $product_id;
            });
    }

    private function createOrder(Customer $customer) : Order
    {
        // create an order
        $order = config('laravel-cart.order_class')::create([
            $customer->getForeignKey() => $customer->id
        ]);

        $cart_items = Cart::items();

        $cart_items->each(function($cart_item) use ($order) {
            $product_key = Str::snake(class_basename(config('laravel-cart.product_class'))).'_id';
            $order->items()->create([
                'name' => $cart_item->getProduct()->getName($cart_item->product_data),
                $product_key => $cart_item->product_id,
                'product_data' => $cart_item->product_data,
                'price' => $cart_item->getProduct()->getPrice($cart_item->product_data),
                'quantity' => $cart_item->quantity
            ]);
        });

        $validDiscounts = $this->getValidDiscounts();

        $validDiscounts->each(function (CartAdjustment $adjustment) use ($order) {
            $order_key = Str::snake(class_basename(config('laravel-cart.order_class'))).'_id';
            $order->adjustments()->create([
                'name' => $adjustment->name,
                'class' => $adjustment->class,
                'parameters' => $adjustment->parameters,
                $order_key => $order->id
            ]);
        });

        Cart::clear();

        return $order;

    }
}

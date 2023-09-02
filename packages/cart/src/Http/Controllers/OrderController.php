<?php

namespace Antidote\LaravelCart\Http\Controllers;

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Models\Order;

class OrderController
{
    private \Antidote\LaravelCart\Domain\Cart $cart;
    public function __construct(\Antidote\LaravelCart\Domain\Cart $cart)
    {
        $this->cart = $cart;
    }
    /**
     * Clears the cart and adds these order items to the current order
     */
    public function setOrderItemsAsCart(int $order_id)
    {
        $existing_order = $this->getOrder($order_id);

        $this->cart->clear();

        $this->populateCart($order_id, $existing_order);

        return redirect('/cart');
    }

    /**
     * Adds the items from this order to the cart
     */
    public function addOrderItemsToCart(int $order_id)
    {
        $existing_order = $this->getOrder($order_id);

        $this->populateCart($order_id, $existing_order);

        return redirect('/cart');
    }

    private function populateCart($order_id, $existing_order)
    {
        $existing_order->items->each(function($order_item) {
            $this->cart->add($order_item->product, $order_item->quantity, $order_item->product_data);
        });

        $this->cart->setActiveOrder($order_id);
    }

    private function getOrder(int $order_id) : Order
    {
        return getClassNameFor('order')::where('id', $order_id)->first() ?? throw new \InvalidArgumentException('The order id is not valid');
    }
}

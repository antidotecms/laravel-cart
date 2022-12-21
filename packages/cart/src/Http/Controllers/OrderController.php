<?php

namespace Antidote\LaravelCart\Http\Controllers;

use Antidote\LaravelCart\Facades\Cart;

class OrderController
{

    /**
     * Clears the cart and adds these order items to the current order
     */
    public function setOrderItemsAsCart(int $order_id)
    {
        $existing_order = getClassNameFor('order')::where('id', $order_id)->first();
        //$current_order = Cart::getActiveOrder();

        Cart::clear();

        //dd($existing_order->items);

        foreach($existing_order->items as $orderitem)
        {
            //dd($orderitem->attributesToArray());
            Cart::add($orderitem->product, $orderitem->quantity, $orderitem->product_data);
        }

        Cart::setActiveOrder($order_id);

        return redirect('/cart');
    }

    /**
     * Adds the items from this order to the cart
     */
    public function addOrderItemsToCart(int $order_id)
    {
        $existing_order = getClassNameFor('order')::where('id', $order_id)->first();

        foreach($existing_order->items as $orderitem)
        {
            //dd($orderitem->attributesToArray());
            Cart::add($orderitem->product, $orderitem->quantity, $orderitem->product_data);
        }

        Cart::setActiveOrder($order_id);

        return redirect('/cart');
    }
}

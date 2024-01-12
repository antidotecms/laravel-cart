<?php

namespace Antidote\LaravelCart\Http\Controllers;

use Antidote\LaravelCart\Domain\Cart;

class PostCheckoutController
{
    public function __invoke()
    {
        $cart = app(Cart::class);

        $order = $cart->getActiveOrder();

        $order->payment->manager()->updateStatus($order);

        $cart->setActiveOrder(null);

        $cart->clear();
    }
}

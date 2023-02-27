<?php

namespace Antidote\LaravelCart\Http\Controllers;

use Antidote\LaravelCart\Contracts\Order;
use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrder;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestPayment;

class OrderCompleteController extends \Illuminate\Routing\Controller
{
    public function __invoke(?Order $order)
    {
        //dump($order->attributesToArray());

        if($order)
        {
            Cart::setActiveOrder(null);

            $order->updateStatus();

            return view(config('laravel-cart.views.order_complete'), [
                'order' => $order
            ]);
        }

        abort(404);
    }
}

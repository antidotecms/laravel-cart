<?php

namespace Antidote\LaravelCart\Http\Controllers;

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrder;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestPayment;

class OrderCompleteController extends \Illuminate\Routing\Controller
{
    private $order;

    //@todo had to inject constructor when using DI - can this be injected into __invoke?
    public function __construct(?Order $order)
    {
        $this->order = $order;
    }

    public function __invoke()
    {
        //dump($order->attributesToArray());

        if($this->order)
        {
            Cart::setActiveOrder(null);

            $this->order->updateStatus();

            return view(config('laravel-cart.views.order_complete'), [
                'order' => $this->order
            ]);
        }

        abort(404);
    }
}

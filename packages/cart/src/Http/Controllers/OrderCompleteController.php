<?php

namespace Antidote\LaravelCart\Http\Controllers;

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestPayment;

class OrderCompleteController extends \Illuminate\Routing\Controller
{
    public function __invoke(\Antidote\LaravelCart\Domain\Cart $cart)
    {
        $order = $this->getOrder() ?? abort(404);

        /** @var Order $order */
        $order->load('items.product.productType');

        $cart->setActiveOrder(null);

        $order->updateStatus();

        return view(config('laravel-cart.views.order_complete'), [
            'order' => $order
        ]);
    }

    private function getOrder() : ?Order
    {
        return getClassNameFor('order')::when(request()->get('order_id'), function($query) {
           return $query->where('id', request()->get('order_id'))
               ->where('customer_id', auth()->guard('customer')->user()->id);
        })->first();
    }
}

<?php

namespace Antidote\LaravelCart\Http\Controllers;

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestPayment;

class OrderCompleteController extends \Illuminate\Routing\Controller
{
    public function __invoke()
    {
        if($order_id = request()->get('order_id')) {
            $order = getClassNameFor('order')::where('id', $order_id)->first();
            if (auth('customer')->check() && $order && $order->customer->id == auth()->guard('customer')->user()->id) {
                $order->load('items.product.productType');
            }
        }

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

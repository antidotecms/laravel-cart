<?php

namespace Antidote\LaravelCart\Http\Controllers;

use Antidote\LaravelCart\Facades\Cart;

class OrderCompleteController extends \Illuminate\Routing\Controller
{
    public function __invoke()
    {
        Cart::setActiveOrder(null);

        if ($order_id = request()->get('order_id')) {

            $order = getClassNameFor('order')::where('id', $order_id)->first();

            if ($order && $order->customer->id == auth()->guard('customer')->user()->id) {
                return view(config('laravel-cart.views.order_complete'), [
                    'order' => $order,
                    'completed' => $order->status == 'Charge Succeeded'
                ]);
            }
        }

        abort(404);
    }


    /**
     * Adds the items from this order to the cart
     *
     * @param int $order_id
     * @return void
     */
    public function addOrderItemsToCart(int $order_id)
    {

    }
}

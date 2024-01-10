<?php

namespace Antidote\LaravelCart\Http\Middleware;

use Antidote\LaravelCart\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrderBelongsToCustomer
{
    public function handle(Request $request, \Closure $next) : Response
    {
        $order = Order::findOrFail($request->get('order_id'));

        if(!$order->customer->id == auth()->user()->id) {
            abort(404);
        }

        return $next($request);
    }
}

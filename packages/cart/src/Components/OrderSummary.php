<?php

namespace Antidote\LaravelCart\Components;

use Antidote\LaravelCart\Models\Order;
use Illuminate\View\Component;
use Illuminate\View\View;

class OrderSummary extends Component
{
    public Order $order;

    public function __construct() {
        $this->order = Order::find(request()->get('order_id'));
    }

    public function render(): View
    {
        return view('laravel-cart::components.order-summary');
    }
}

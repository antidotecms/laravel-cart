<?php

namespace Antidote\LaravelCart\Components\OrderSummary;

use Antidote\LaravelCart\Models\Order;
use Illuminate\View\Component;
use Illuminate\View\View;
use function Filament\Support\format_money;

class Totals extends Component
{
    public string $subtotal;
    public string $tax;
    public string $total;

    public function __construct(Order $order)
    {
        $this->subtotal = format_money($order->subtotal, 'GBP', 100);
        $this->tax = format_money($order->tax, 'GBP', 100);
        $this->total = format_money($order->total, 'GBP', 100);
    }

    public function render(): View
    {
        return view('laravel-cart::components.order-summary.totals');
    }
}

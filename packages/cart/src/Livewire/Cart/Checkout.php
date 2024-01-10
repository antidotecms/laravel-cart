<?php

namespace Antidote\LaravelCart\Livewire\Cart;

use Livewire\Component;

class Checkout extends Component
{
    public string $view = '';

    public function mount()
    {
        //$this->view = $type->manager()->getCheckoutComponent();
        $this->view = app(\Antidote\LaravelCart\Domain\Cart::class)->getActiveOrder()->payment->payment_method_type->manager()->getCheckoutComponent();
    }

    public function render()
    {
        return view('laravel-cart::livewire.cart.checkout');
    }
}

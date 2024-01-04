<?php

namespace Antidote\LaravelCart\Livewire\Cart;

use Antidote\LaravelCartFilament\CartPanelPlugin;
use Livewire\Attributes\On;
use Livewire\Component;

class Icon extends Component
{
    public int $count = 0;
    public string $cartUrl = '';

    public function mount()
    {
        $this->cartUrl = CartPanelPlugin::get('urls.cart');
        $this->updateCartCount();
    }

    #[On('updateCartCount')]
    public function updateCartCount()
    {
        $this->count = count(app(\Antidote\LaravelCart\Domain\Cart::class)->items());
    }

    public function render()
    {
        return view('laravel-cart::livewire.cart.icon');
    }
}

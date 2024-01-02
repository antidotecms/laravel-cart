<?php

namespace Antidote\LaravelCart\Livewire\Cart;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use function Filament\Support\format_money;

class Cart extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $cartItems;

    public function mount()
    {
        $this->update();
    }

    #[On('update')]
    public function update()
    {
        $cart = app(\Antidote\LaravelCart\Domain\Cart::class);
        $this->cartItems = $cart->items()->keys()->toArray();
    }

    public function render()
    {
        return view('laravel-cart::livewire.cart.index');
    }

    #[Computed]
    public function subtotal(): string
    {
        return format_money(app(\Antidote\LaravelCart\Domain\Cart::class)->getSubtotal(), 'GBP', 100);
    }

    #[Computed]
    public function tax(): string
    {
        return format_money(app(\Antidote\LaravelCart\Domain\Cart::class)->getTax(), 'GBP', 100);
    }

    #[Computed]
    public function total(): string
    {
        return format_money(app(\Antidote\LaravelCart\Domain\Cart::class)->getTotal(), 'GBP', 100);
    }
}

<?php

namespace Antidote\LaravelCart\Livewire\Cart;

use Antidote\LaravelCart\Enums\PaymentMethod;
use Antidote\LaravelCart\Models\Payment;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Filament\Forms\Components\Actions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use Livewire\Component;

class CheckoutOptions extends Component implements HasForms
{
    use InteractsWithForms;

    public function form(Form $form): Form
    {

        return $form->schema([
            Actions::make($this->getCheckoutActions())
        ]);
    }

    private function getCheckoutActions(): array
    {
        return collect(PaymentMethod::cases())->map(function(PaymentMethod $type) {
            return Actions\Action::make($type->name)
                ->button()
                ->label("Checkout with ".Str::ucfirst( $type->name))
                ->action(fn() => $this->navigateToCheckout($type));
        })->toArray();
    }

    public function navigateToCheckout(PaymentMethod $type)
    {
        if(!auth('customer')->check()) {
            throw new \Exception('must be logged in as customer');
        }

        $order = app(\Antidote\LaravelCart\Domain\Cart::class)->createOrder(auth('customer')->user());

        $payment = Payment::make([
            'payment_method_type' => $type
        ]);

        $order->payment()->save($payment);

        app(\Antidote\LaravelCart\Domain\Cart::class)->setActiveOrder($order);

        return redirect(CartPanelPlugin::get('urls.checkout'));
    }

    public function render()
    {
        return view('laravel-cart::livewire.cart.checkout-options');
    }
}

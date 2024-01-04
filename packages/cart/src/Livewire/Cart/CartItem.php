<?php

namespace Antidote\LaravelCart\Livewire\Cart;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use function Filament\Support\format_money;

/**
 * @property \Antidote\LaravelCart\DataTransferObjects\CartItem $cartItem
 * @property Form $form
 */
class CartItem extends Component implements HasForms
{
    use InteractsWithForms;

    public int $quantity;
    public int $cartitemId;

    public function mount(int $cartitem_id): void
    {
        $this->cartitemId = $cartitem_id;
        $this->update();
    }

    #[Computed]
    public function lineTotal()
    {
        $lineTotal = $this->cartItem->quantity * $this->cartItem->getProduct()->getPrice($this->cartItem->product_data);
        return format_money($lineTotal, 'GBP', 100);
    }

    #[Computed]
    public function productName()
    {
        return $this->cartItem->getProduct()->name;
    }

    #[Computed]
    public function productDescription()
    {
        return $this->cartItem->getProduct()->getDescription($this->cartItem->product_data);
    }

    #[On('update')]
    public function update(): void
    {
        $this->quantity = $this->cartItem->quantity;
    }

    #[Computed]
    public function cartItem()
    {
        return app(\Antidote\LaravelCart\Domain\Cart::class)->items()->filter(fn($item, $key) => $key == $this->cartitemId)->first();
    }

    public function removeFromCart(): void
    {
        $product = $this->cartItem->getProduct();
        app(\Antidote\LaravelCart\Domain\Cart::class)->remove($product, null, $this->cartItem->product_data);

        Notification::make()
            ->success()
            ->title('Item removed from cart')
            ->send();

        $this->dispatch('updateCartCount');

        $this->dispatch('update')->to(Cart::class);
    }

    public function updateQuantity(): void
    {
        //don't bother updating the quantity hasn't changed
        if($this->form->getState()['quantity'] == $this->cartItem->quantity) {
            return;
        }

        $this->form->validate();

        $product = $this->cartItem->getProduct();
        app(\Antidote\LaravelCart\Domain\Cart::class)->updateQuantity($product, $this->quantity, $this->cartItem->product_data);

        Notification::make()
            ->success()
            ->title('Item quantity updated')
            ->send();

        $this->dispatch('update')->to(Cart::class);
        $this->dispatch('update', $this->cartitemId)->self();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('quantity')
                    ->inlineLabel()
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->step(1)
                    ->live()
                    ->suffixActions([
                        Action::make('update')
                            ->icon('heroicon-m-pencil-square')
                            ->disabled(fn($state) => $state == $this->cartItem->quantity)
                            ->link()
                            ->size(ActionSize::Large)
                            ->action(function() {
                                $this->updateQuantity();
                            }),
                        Action::make('remove')
                            ->icon('heroicon-m-pencil-square')
                            ->link()
                            ->size(ActionSize::Large)
                            ->action(function() {
                                $this->removeFromCart();
                            })
                        ]
                    ),
            ]);
    }
    public function render()
    {
        return view('laravel-cart::livewire.cart.item');
    }
}

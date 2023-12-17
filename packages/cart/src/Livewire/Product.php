<?php

namespace Antidote\LaravelCart\Livewire;

use Antidote\LaravelCart\Domain\Cart;
use Antidote\LaravelCart\Models\Product as ProductModel;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

/**
 * @property Form $form
 */
class Product extends Component implements HasForms
{
    use InteractsWithForms;

    private ProductModel $product;
    public int $productId;
    public ?array $data = [];
    public ?int $price;
    public int $quantity = 1;

    public function mount(ProductModel $product): void
    {
        $this->product = $product;
        $this->productId = $product->id;
        $this->form->fill();
    }

    protected function getForms(): array
    {
        return [
            'form',
            'addToCartForm'
        ];
    }

    public function form(Form $form): Form
    {
        $this->product = ProductModel::find($this->productId);

        return $form
            ->schema($this->product->productType->clientForm())
            ->statePath('data');
    }

    public function addToCartForm(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('quantity')
                    ->hiddenLabel()
                    ->numeric()
                    ->default(1)
            ]);
    }

    public function addToCart()
    {
        $cart = app(Cart::class);
        $this->product = ProductModel::find($this->productId);
        $cart->add($this->product, $this->quantity, $this->data);

        Notification::make()
            ->success()
            ->title('Item added to cart')
            ->body('View your cart')
            ->send();
    }

    public function getPrice()
    {
        $this->product = ProductModel::find($this->productId);
        return $this->product->getPrice($this->data);
    }

    public function render()
    {
        return view('laravel-cart::livewire.product');
    }
}

<?php

namespace Antidote\LaravelCart\Livewire;

use Antidote\LaravelCart\Domain\Cart;
use Antidote\LaravelCart\Models\Product as ProductModel;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;
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
    }

    public function form(Form $form): Form
    {
        $this->product = ProductModel::find($this->productId);

        $schema = array_merge(
            $this->productForm(),
            $this->addToCartForm()
        );

        return $form
            ->schema($schema);

    }

    public function productForm(): array
    {
        return [
            Group::make($this->product->productType->clientForm())
            ->statePath('data')
        ];
    }

    public function addToCartForm(): array
    {
        return [
            Group::make([
                TextInput::make('quantity')
                    ->hiddenLabel()
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->step(1)
                    ->default(1)
                    ->suffixAction(
                        Action::make('AddToCart')
                            ->icon('heroicon-m-pencil-square')
                            ->link()
                            ->size(ActionSize::Large)
                            ->action(function() {
                                $this->addToCart();
                            })
                    ),
            ])
        ];
    }

    public function addToCart()
    {
        $this->form->validate();

        $cart = app(Cart::class);
        $this->product = ProductModel::find($this->productId);
        $cart->add($this->product, $this->quantity, $this->data);

        $this->form->fill();

        $cartUrl = CartPanelPlugin::get('urls.cart') ?? '/cart';

        Notification::make()
            ->success()
            ->title('Item added to cart')
            ->body("<a href='$cartUrl'>View your cart</a>")
            ->send();
    }

    public function render()
    {
        return view('laravel-cart::livewire.product.index');
    }
}

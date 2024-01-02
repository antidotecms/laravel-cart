<?php

use Filament\Notifications\Notification;

it('will populate the correct properties', function() {
   $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct([
       'price' => 1999
   ])->create([
       'name' => 'A Very Simple Product'
   ]);

   \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Product::class, [
           'product' => $product
       ])
       ->assertSet('productId', $product->id)
       ->assertSet('data', [])
       ->assertSet('price', $product->price)
       ->assertSet('quantity', 1);
})
->covers(\Antidote\LaravelCart\Livewire\Product::class);

it('will display the client form', function () {

    $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create([
        'name' => 'A Very Simple Product'
    ]);

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Product::class, [
        'product' => $product
    ])
    ->assertFormFieldExists('data.colour', function(\Filament\Forms\Components\Select $field) {
        return $field->getOptions() == [
            'red',
            'blue'
        ] && $field->isRequired();
    });
})
->covers(\Antidote\LaravelCart\Livewire\Product::class);

it('will display the add to cart form', function () {

    $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create([
        'name' => 'A Very Simple Product'
    ]);

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Product::class, [
        'product' => $product
    ])
        ->assertFormFieldExists('quantity', function(\Filament\Forms\Components\TextInput $field) {

            $suffix_action = collect($field->getSuffixActions())->first();
            $valid_suffix_action = $suffix_action->getIcon() == 'heroicon-m-pencil-square' &&
                $suffix_action->isLink() &&
                $suffix_action->getSize() == \Filament\Support\Enums\ActionSize::Large;

            return $field->isLabelHidden() &&
                $field->isNumeric() &&
                $field->isRequired() &&
                $field->getMinValue() == 1 &&
                $field->getStep() == 1 &&
                $field->getDefaultState() == 1
                && $valid_suffix_action;
        });
})
->covers(\Antidote\LaravelCart\Livewire\Product::class);;

it('will add a product to the cart', function () {

    $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create([
        'name' => 'A Very Simple Product'
    ]);

    $cart = app(\Antidote\LaravelCart\Domain\Cart::class);
    expect(count($cart->items()))->toBe(0);

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Product::class, [
        'product' => $product
    ])
        ->set('quantity', 2)
        ->set('data.colour', 'red')
        ->assertFormFieldExists('quantity', function(\Filament\Forms\Components\TextInput $field) {
            /* @var \Filament\Forms\Components\Actions\Action $suffix_action */
            $suffix_action = collect($field->getSuffixActions())->first();
            $suffix_action->call();
            return true;
        })
        ->assertHasNoFormErrors();

    $cart = app(\Antidote\LaravelCart\Domain\Cart::class);
    expect(count($cart->items()))->toBe(1);
})
->covers(\Antidote\LaravelCart\Livewire\Product::class);;

it('will add a notification when an item is added to the cart', function () {

    $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create([
        'name' => 'A Very Simple Product'
    ]);

    $cart = app(\Antidote\LaravelCart\Domain\Cart::class);
    expect(count($cart->items()))->toBe(0);

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Product::class, [
        'product' => $product
    ])
        ->set('quantity', 2)
        ->set('data.colour', 'red')
        ->call('addToCart')
        ->assertNotified(
            Notification::make()
                ->success()
                ->title('Item added to cart')
                ->body("<a href='cart'>View your cart</a>")
        );
})
->covers(\Antidote\LaravelCart\Livewire\Product::class);;

it('will use the custom cart url when displaying the notification when an item is added to the cart', function () {

    \Antidote\LaravelCartFilament\CartPanelPlugin::set('urls.cart', 'some-other-cart-url');

    $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create([
        'name' => 'A Very Simple Product'
    ]);

    $cart = app(\Antidote\LaravelCart\Domain\Cart::class);
    expect(count($cart->items()))->toBe(0);

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Product::class, [
        'product' => $product
    ])
        ->set('quantity', 2)
        ->set('data.colour', 'red')
        ->call('addToCart')
        ->assertNotified(
            Notification::make()
                ->success()
                ->title('Item added to cart')
                ->body("<a href='some-other-cart-url'>View your cart</a>")
        );
})
->covers(\Antidote\LaravelCart\Livewire\Product::class);;

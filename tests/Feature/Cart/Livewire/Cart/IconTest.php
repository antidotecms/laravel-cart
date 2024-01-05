<?php

it('displays the number of items in the cart', function() {

    $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create([
        'name' => 'Simple Product'
    ]);

    /** @var \Antidote\LaravelCart\Domain\Cart $cart */
    $cart = app(\Antidote\LaravelCart\Domain\Cart::class);
    $cart->add($product);

    expect(count($cart->items()))->toBe(1);

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\Icon::class)
        ->assertSet('count', 1);
})
->covers(\Antidote\LaravelCart\Livewire\Cart\Icon::class);

it('provides the correct link to the cart', function () {

    \Antidote\LaravelCartFilament\CartPanelPlugin::set('urls.cart', 'some-url');

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\Icon::class)
        ->assertSet('cartUrl', 'some-url');
})
->covers(\Antidote\LaravelCart\Livewire\Cart\Icon::class);

it('will update the cart count', function () {

    $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create([
        'name' => 'Simple Product'
    ]);

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\Icon::class)
        ->assertSet('count', 0);

    /** @var \Antidote\LaravelCart\Domain\Cart $cart */
    $cart = app(\Antidote\LaravelCart\Domain\Cart::class);
    $cart->add($product);

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\Icon::class)
        ->dispatch('updateCartCount')
        ->assertSet('count', 1);
})
->covers(\Antidote\LaravelCart\Livewire\Cart\Icon::class);

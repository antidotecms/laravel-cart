<?php

beforeEach(function() {

    $this->product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create([
        'name' => 'A Very Simple Product'
    ]);

    $this->cart = app(\Antidote\LaravelCart\Domain\Cart::class);
    $this->cart->add($this->product, 1, []);
});

it('will populate the cart items', function() {

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\Cart::class)
        ->assertSet('cartItems', $this->cart->items()->keys()->toArray());
})
->covers(\Antidote\LaravelCart\Livewire\Cart\Cart::class);

it('will provide the subtotal', function () {

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\Cart::class)
        ->assertSet('subtotal', "£19.99");
})
->covers(\Antidote\LaravelCart\Livewire\Cart\Cart::class);

it('will provide the tax', function () {

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\Cart::class)
        ->assertSet('tax', "£4.00");
})
->covers(\Antidote\LaravelCart\Livewire\Cart\Cart::class);

it('will provide the total', function () {

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\Cart::class)
        ->assertSet('total', "£23.99");
});

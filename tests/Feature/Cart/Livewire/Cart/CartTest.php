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

it('will show the checkout options if the customer is logged in', function () {

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\Cart::class)
        ->assertDontSeeLivewire(\Antidote\LaravelCart\Livewire\Cart\CheckoutOptions::class)
        ->assertSee("To checkout, you need to <a href='".\Antidote\LaravelCartFilament\CartPanelPlugin::get('urls.login')."'>login.", false);

    $this->actingAs($customer, 'customer');

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\Cart::class)
        ->assertSeeLivewire(\Antidote\LaravelCart\Livewire\Cart\CheckoutOptions::class);
});

it('will not display the checkout options if there are no items in the cart', function () {

    $this->cart->remove($this->product);

    expect(count($this->cart->items()))->toBe(0);

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\Cart::class)
        ->assertDontSeeLivewire(\Antidote\LaravelCart\Livewire\Cart\CheckoutOptions::class);
});

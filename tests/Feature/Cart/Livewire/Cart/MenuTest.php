<?php

it('will log a user out', function() {

    $this->be(\Antidote\LaravelCart\Models\Customer::factory()->create(), 'customer');

    expect(\Illuminate\Support\Facades\Auth::check('customer'))->toBeTrue();

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Customer\Menu::class)
        ->callAction('logout');

    expect(\Illuminate\Support\Facades\Auth::check('customer'))->toBeFalse();
})
->covers(\Antidote\LaravelCart\Livewire\Customer\Menu::class);

it('will navigate to the dashboard', function () {

    $this->be(\Antidote\LaravelCart\Models\Customer::factory()->create(), 'customer');

    expect(\Illuminate\Support\Facades\Auth::check('customer'))->toBeTrue();

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Customer\Menu::class)
        ->callAction('home')
        ->assertRedirect(\Antidote\LaravelCartFilament\CartPanelPlugin::get('urls.customer').'/dashboard');

})
->covers(\Antidote\LaravelCart\Livewire\Customer\Menu::class);

it('will redirect to the login page', function () {

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Customer\Menu::class)
        ->callAction('login')
        ->assertRedirect(\Antidote\LaravelCartFilament\CartPanelPlugin::get('urls.customer').'/login');
})
->covers(\Antidote\LaravelCart\Livewire\Customer\Menu::class);

test('menu items change depending on whether user is logged in or not', function () {

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Customer\Menu::class)
        ->assertActionHidden('home')
        ->assertActionHidden('logout')
        ->assertActionVisible('login');

    $this->be(\Antidote\LaravelCart\Models\Customer::factory()->create(), 'customer');

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Customer\Menu::class)
        ->assertActionVisible('home')
        ->assertActionVisible('logout')
        ->assertActionHidden('login');
});

test('the contents of the cart are preserved after logout', function () {

    $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleproduct([
        'price' => 1000
    ])->create([
        'name' => ' A Simple Product'
    ]);

    $this->be(\Antidote\LaravelCart\Models\Customer::factory()->create(), 'customer');

    $cart = app(\Antidote\LaravelCart\Domain\Cart::class);

    $cart->add($product);

    expect(count($cart->items()))->toBe(1);

    expect(auth()->guard('customer')->check())->toBeTrue();

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Customer\Menu::class)
        ->callAction('logout');

    expect(auth()->guard('customer')->check())->toBeFalse();

    expect(count($cart->items()))->toBe(1);
})
->covers(\Antidote\LaravelCart\Livewire\Customer\Menu::class);

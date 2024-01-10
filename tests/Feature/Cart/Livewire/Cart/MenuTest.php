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

});

it('will redirect to the login page', function () {

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Customer\Menu::class)
        ->callAction('login')
        ->assertRedirect(\Antidote\LaravelCartFilament\CartPanelPlugin::get('urls.customer').'/login');
});

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

<?php

beforeEach(function() {

    $this->customer = \Antidote\LaravelCart\Models\Customer::factory()->create([
        'email' => 'customer@somedomain.co.uk',
        'password' => \Illuminate\Support\Facades\Hash::make('password')
    ]);

    expect(\Illuminate\Support\Facades\Auth::guard('customer')->check())->toBeFalse();
});

it('will log a user in', function() {

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Customer\Login::class)
        ->set('data.email', $this->customer->email)
        ->set('data.password', 'password')
        ->call('login')
        ->assertHasNoFormErrors()
        ->assertRedirect('/yup');

    expect(\Illuminate\Support\Facades\Auth::guard('customer')->check())->toBeTrue();
})
->covers(\Antidote\LaravelCart\Livewire\Customer\Login::class);

it('will display an error if login was unsuccessful', function () {

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Customer\Login::class)
        ->set('data.email', $this->customer->email)
        ->set('data.password', 'not the password')
        ->call('login')
        ->assertHasErrors(['fail'])
        ->assertSee('no such user');

    expect(\Illuminate\Support\Facades\Auth::guard('customer')->check())->toBeFalse();
})
->covers(\Antidote\LaravelCart\Livewire\Customer\Login::class);

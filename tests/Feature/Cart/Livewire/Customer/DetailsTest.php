<?php

use function Pest\Livewire\livewire;

beforeEach(function() {


    $this->customer = \Antidote\LaravelCart\Models\Customer::factory()->create([
        'email' => 'customer@somedomain.co.uk',
        'password' => \Illuminate\Support\Facades\Hash::make('password')
    ]);

    $this->actingAs($this->customer, 'customer');
});

it('will display the form to amend details', function () {

    livewire(\Antidote\LaravelCart\Livewire\Customer\Details::class)
        ->assertFormFieldExists('name', function(\Filament\Forms\Components\TextInput $field) {
            return $field->isRequired();
        })
        ->assertFormFieldExists('email', function(\Filament\Forms\Components\TextInput $field) {
            return $field->isRequired() && $field->isDisabled();
        });
})
->covers(\Antidote\LaravelCart\Livewire\Customer\Details::class);

it('will update the users details', function () {

    livewire(\Antidote\LaravelCart\Livewire\Customer\Details::class)
        ->set('data.name', 'a different name')
        ->call('save')
        ->assertDispatchedTo(\Antidote\LaravelCart\Livewire\Customer\Dashboard::class, 'detailsUpdated')
        ->assertHasNoFormErrors()
        ->assertSet('data.name', 'a different name');

    $this->customer->refresh();

    expect($this->customer->name)->toBe('a different name');
})
->covers(\Antidote\LaravelCart\Livewire\Customer\Details::class);

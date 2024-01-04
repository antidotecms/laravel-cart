<?php

use function Pest\Livewire\livewire;

beforeEach(function() {


    $this->customer = \Antidote\LaravelCart\Models\Customer::factory()->create([
        'email' => 'customer@somedomain.co.uk',
        'password' => \Illuminate\Support\Facades\Hash::make('password')
    ]);

    $this->actingAs($this->customer, 'customer');
});

it('will display the customers name', function() {

    livewire(\Antidote\LaravelCart\Livewire\Customer\Dashboard::class)
        ->assertSee("Welcome {$this->customer->name}");
})
->covers(\Antidote\LaravelCart\Livewire\Customer\Dashboard::class);

it('will display the form to amend details', function () {

    livewire(\Antidote\LaravelCart\Livewire\Customer\Dashboard::class)
        ->assertFormFieldExists('name', function(\Filament\Forms\Components\TextInput $field) {
            return $field->isRequired();
        })
        ->assertFormFieldExists('email', function(\Filament\Forms\Components\TextInput $field) {
            return $field->isRequired() && $field->isDisabled();
        });
});

it('will update the users details', function () {

    livewire(\Antidote\LaravelCart\Livewire\Customer\Dashboard::class)
        ->set('data.name', 'a different name')
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertSet('data.name', 'a different name');

    $this->customer->refresh();

    expect($this->customer->name)->toBe('a different name');
});

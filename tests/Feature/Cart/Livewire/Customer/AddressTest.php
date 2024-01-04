<?php

use Filament\Notifications\Notification;
use function Pest\Livewire\livewire;

beforeEach(function() {

    $this->customer = \Antidote\LaravelCart\Models\Customer::factory()->create([
        'email' => 'customer@somedomain.co.uk',
        'password' => \Illuminate\Support\Facades\Hash::make('password')
    ]);

    $this->actingAs($this->customer, 'customer');
});

it('will populate the data', function() {

    livewire(\Antidote\LaravelCart\Livewire\Customer\Address::class)
        ->assertSet('data.line_1', $this->customer->address->line_1)
        ->assertSet('data.line_2', $this->customer->address->line_2)
        ->assertSet('data.town_city', $this->customer->address->town_city)
        ->assertSet('data.county', $this->customer->address->county)
        ->assertSet('data.postcode', $this->customer->address->postcode);
})
->covers(\Antidote\LaravelCart\Livewire\Customer\Address::class);

it('has the correct form', function () {

    livewire(\Antidote\LaravelCart\Livewire\Customer\Address::class)
        ->assertFormFieldExists('line_1', function(\Filament\Forms\Components\TextInput $field) {
            return $field->isRequired();
        })
        ->assertFormFieldExists('line_2', function(\Filament\Forms\Components\TextInput $field) {
            return true;
        })
        ->assertFormFieldExists('town_city', function(\Filament\Forms\Components\TextInput $field) {
            return $field->isRequired();
        })
        ->assertFormFieldExists('county', function(\Filament\Forms\Components\TextInput $field) {
            return $field->isRequired();
        })
        ->assertFormFieldExists('postcode', function(\Filament\Forms\Components\TextInput $field) {
            return $field->isRequired();
        });
})
->covers(\Antidote\LaravelCart\Livewire\Customer\Address::class);

it('will update an address', function () {

    $address = \Antidote\LaravelCart\Models\Address::factory()->make();

    livewire(\Antidote\LaravelCart\Livewire\Customer\Address::class)
        ->fillForm($address->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    $this->customer->refresh();

    expect($this->customer->address->line_1)->toBe($address->line_1);
    expect($this->customer->address->line_2)->toBe($address->line_2);
    expect($this->customer->address->town_city)->toBe($address->town_city);
    expect($this->customer->address->county)->toBe($address->county);
    expect($this->customer->address->postcode)->toBe($address->postcode);
});

it('will send a notifictaion when the address is updated', function () {

    $address = \Antidote\LaravelCart\Models\Address::factory()->make();

    livewire(\Antidote\LaravelCart\Livewire\Customer\Address::class)
        ->fillForm($address->toArray())
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified(
            Notification::make()
                ->title('Address Updated')
                ->success()
        );


});

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

    $this->markTestSkipped('No longer provided');

    livewire(\Antidote\LaravelCart\Livewire\Customer\Dashboard::class)
        ->assertSet('name', $this->customer->name)
        ->assertSee("Welcome {$this->customer->name}");

})
->covers(\Antidote\LaravelCart\Livewire\Customer\Dashboard::class);



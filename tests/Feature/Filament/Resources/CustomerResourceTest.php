<?php

use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestCustomer;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestUser;
use function Pest\Livewire\livewire;

it('will list the customers', function() {

    Config::set('laravel-cart.classes.customer', TestCustomer::class);

    $customers = TestCustomer::factory()->count(10)->create();

    $user = TestUser::create([
        'name' => 'Test User',
        'email' => 'test@user.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password')
    ]);

    $this->actingAs($user)->get(\Antidote\LaravelCartFilament\Resources\CustomerResource::getUrl('index'))->assertSuccessful();

    livewire(\Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\ListCustomers::class)
        ->assertCanSeeTableRecords($customers);

});

it('can render the edit page', function () {

    Config::set('laravel-cart.classes.customer', TestCustomer::class);

    $customer = TestCustomer::factory()->create();

    $user = TestUser::create([
        'name' => 'Test User',
        'email' => 'test@user.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password')
    ]);

    $response = $this->actingAs($user)->get(\Antidote\LaravelCartFilament\Resources\CustomerResource::getUrl('edit', [
        'record' => $customer->getKey()
    ]))->assertSuccessful();

    //dump($response);
});

it('has the required fields', function () {

    Config::set('laravel-cart.classes.customer', TestCustomer::class);

    $customer = TestCustomer::factory()->create();

    livewire(\Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\EditCustomer::class, [
        'record' => $customer->getKey()
    ])
        ->assertFormSet([
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email
        ]);
});

it('will allow overriding of the customer resource', function () {

    Config::set('laravel-cart.classes.customer', TestCustomer::class);
    Config::set('laravel-cart.filament.order', \Antidote\LaravelCart\Tests\Fixtures\App\Filament\Resources\TestCustomerResource::class);

    $customer = TestCustomer::factory()->create();

    $customer->address = 'an address';
    $customer->save();

    livewire(\Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\EditCustomer::class, [
        'record' => $customer->getKey()
    ])
        ->assertFormSet([
            'address' => 'an address'
        ]);
});

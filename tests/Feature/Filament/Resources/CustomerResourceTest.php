<?php

use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestUser;
use function Pest\Livewire\livewire;

beforeEach(function() {

    $this->user = TestUser::create([
        'name' => 'Test User',
        'email' => 'test@user.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password')
    ]);
});

it('will list the customers', function() {

    Config::set('laravel-cart.classes.customer', \Antidote\LaravelCart\Models\Customer::class);

    $customers = \Antidote\LaravelCart\Models\Customer::factory()->count(10)->create();

    $this->actingAs($this->user)->get(\Antidote\LaravelCartFilament\Resources\CustomerResource::getUrl('index'))->assertSuccessful();

    livewire(\Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\ListCustomers::class)
        ->assertCanSeeTableRecords($customers);

})
->coversClass(\Antidote\LaravelCartFilament\Resources\CustomerResource::class);

it('can render the edit page', function () {

    //Config::set('laravel-cart.classes.customer', \Antidote\LaravelCart\Models\Customer::class);

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $response = $this->actingAs($this->user)->get(\Antidote\LaravelCartFilament\Resources\CustomerResource::getUrl('edit', [
        'record' => $customer->getKey()
    ]))->assertSuccessful();

    //dump($response);
})
->coversClass(\Antidote\LaravelCartFilament\Resources\CustomerResource::class);

it('has the required fields', function () {

    //Config::set('laravel-cart.classes.customer', \Antidote\LaravelCart\Models\Customer::class);

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    livewire(\Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\EditCustomer::class, [
        'record' => $customer->getKey()
    ])
        ->assertFormSet([
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email
        ]);
})
->coversClass(\Antidote\LaravelCartFilament\Resources\CustomerResource::class);

it('renders the form correctly', function () {

    livewire(\Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\CreateCustomer::class)
        //returning true here validates the field type as we must pass in a TextInput
        ->assertFormFieldExists('id', function(\Filament\Forms\Components\TextInput $field) {
            return true;
        })
        ->assertFormFieldExists('name', function(\Filament\Forms\Components\TextInput $field) {
            return true;
        })
        ->assertFormFieldExists('email', function(\Filament\Forms\Components\TextInput $field) {
            return $field->isEmail();
        });
})
->covers(\Antidote\LaravelCartFilament\Resources\CustomerResource::class);

it('will provide the correct urls', function () {

    $this->actingAs($this->user)->get(\Antidote\LaravelCartFilament\Resources\CustomerResource::getUrl('index'))
        ->assertSuccessful();

    $this->actingAs($this->user)->get(\Antidote\LaravelCartFilament\Resources\CustomerResource::getUrl('create'))
        ->assertSuccessful();

    $this->actingAs($this->user)->get(\Antidote\LaravelCartFilament\Resources\CustomerResource::getUrl('edit', [
        'record' => \Antidote\LaravelCart\Models\Customer::factory()->create()->id
    ]))->assertSuccessful();
})
->covers(\Antidote\LaravelCartFilament\Resources\CustomerResource::class);

test('listing customers will use the correct resource', function () {

    expect(\Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\ListCustomers::getResource())
        ->toBe(\Antidote\LaravelCartFilament\Resources\CustomerResource::class);
})
->covers(\Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\ListCustomers::class);

test('editing customers will use the correct resource', function () {

    expect(\Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\EditCustomer::getResource())
        ->toBe(\Antidote\LaravelCartFilament\Resources\CustomerResource::class);
})
->covers(\Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\EditCustomer::class);

test('creating customers will use the correct resource', function () {

    expect(\Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\CreateCustomer::getResource())
        ->toBe(\Antidote\LaravelCartFilament\Resources\CustomerResource::class);
})
->covers(\Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\CreateCustomer::class);

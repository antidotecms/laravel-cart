<?php

use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestUser;
use function Pest\Livewire\livewire;

beforeEach(function() {

    $this->product = TestProduct::factory()->asSimpleProduct()->create();
    $this->customer = \Antidote\LaravelCart\Models\Customer::factory()->create();
    $this->orders = \Antidote\LaravelCart\Models\Order::factory()
        ->count(10)
        ->withProduct($this->product)
        ->forCustomer($this->customer)
        ->create([
            'status' => 'an order status'
        ]);

    $this->user = TestUser::create([
        'name' => 'Test User',
        'email' => 'test@user.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password')
    ]);

});

it('will list the orders', function () {

    livewire(\Antidote\LaravelCartFilament\Resources\OrderResource\Pages\ListOrders::class)
        ->assertCanSeeTableRecords($this->orders);
})
->coversClass(\Antidote\LaravelCartFilament\Resources\OrderResource::class)
->group('order_resource_table_records');

it('has the correct columns', function () {

    livewire(\Antidote\LaravelCartFilament\Resources\OrderResource\Pages\ListOrders::class)
        ->assertTableColumnStateSet('id', $this->orders->first()->id, $this->orders->first())
        ->assertTableColumnStateSet('order_total', $this->orders->first()->total, $this->orders->first())
        ->assertTableColumnStateSet('status', $this->orders->first()->status, $this->orders->first())
        ->assertTableColumnStateSet('customer.name', $this->customer->name, $this->orders->first());
})
->coversClass(\Antidote\LaravelCartFilament\Resources\OrderResource::class)
->group('order_resource_table_columns');

it('can render the pages', function () {

    $this->actingAs($this->user)->get(\Antidote\LaravelCartFilament\Resources\OrderResource::getUrl('index'))
        ->assertSuccessful();

    $this->actingAs($this->user)->get(\Antidote\LaravelCartFilament\Resources\OrderResource::getUrl('create'))
        ->assertForbidden();

    $this->actingAs($this->user)->get(\Antidote\LaravelCartFilament\Resources\OrderResource::getUrl('edit', [
        'record' => $this->orders->first()->getKey()
    ]))->assertSuccessful();
})
->coversClass(\Antidote\LaravelCartFilament\Resources\OrderResource::class)
->group('order_resource_urls');

it('has the required fields', function () {

    livewire(\Antidote\LaravelCartFilament\Resources\OrderResource\Pages\EditOrder::class, [
        'record' => $this->orders->first()->getKey()
    ])
    ->assertFormSet([
        'id' => $this->orders->first()->id,
        'customer' => $this->customer->id,
        //@todo would be nice just to assert the state (i.e the integer value) rather than have to format and assert - maybe use a mask?
        'order_subtotal' => NumberFormatter::create('en_GB', NumberFormatter::CURRENCY)->formatCurrency($this->orders->first()->subtotal/100, 'GBP'),
        'order_total' => NumberFormatter::create('en_GB', NumberFormatter::CURRENCY)->formatCurrency($this->orders->first()->total/100, 'GBP'),
        'tax' => NumberFormatter::create('en_GB', NumberFormatter::CURRENCY)->formatCurrency($this->orders->first()->tax/100, 'GBP'),
        'status' => $this->orders->first()->status
    ])
    ->call('save')
    ->assertHasNoErrors();
})
->coversClass(\Antidote\LaravelCartFilament\Resources\OrderResource::class)
->group('order_resource_form_fields_all');

test('the id field is disabled', function () {

    livewire(\Antidote\LaravelCartFilament\Resources\OrderResource\Pages\EditOrder::class, [
        'record' => $this->orders->first()->id
    ])
    ->assertFormFieldIsDisabled('id');
})
->coversClass(\Antidote\LaravelCartFilament\Resources\OrderResource::class);

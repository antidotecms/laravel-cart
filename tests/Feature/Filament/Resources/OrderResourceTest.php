<?php

use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestUser;
use Antidote\LaravelCartFilament\Resources\OrderResource;
use Antidote\LaravelCartFilament\Resources\OrderResource\Pages\EditOrder;
use Antidote\LaravelCartFilament\Resources\OrderResource\Pages\ListOrders;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;
use function Pest\Livewire\livewire;

beforeEach(function() {

    $this->product = TestProduct::factory()->asSimpleProduct()->create();
    $this->customer = Customer::factory()->create();
    $this->orders = Order::factory()
        ->count(10)
        ->withProduct($this->product)
        ->forCustomer($this->customer)
        ->create([
            'status' => 'an order status'
        ]);

    $this->user = TestUser::create([
        'name' => 'Test User',
        'email' => 'test@user.com',
        'password' => Hash::make('password')
    ]);

});

it('will list the orders', function () {

    livewire(ListOrders::class)
        ->assertCanSeeTableRecords($this->orders);
})
->coversClass(OrderResource::class)
->group('order_resource_table_records');

it('has the correct columns', function () {

    livewire(ListOrders::class)
        ->assertTableColumnStateSet('id', $this->orders->first()->id, $this->orders->first())
        ->assertTableColumnStateSet('order_total', $this->orders->first()->total, $this->orders->first())
        ->assertTableColumnStateSet('status', $this->orders->first()->status, $this->orders->first())
        ->assertTableColumnStateSet('customer.name', $this->customer->name, $this->orders->first());
})
->coversClass(OrderResource::class)
->group('order_resource_table_columns');

it('can render the pages', function () {

    $this->actingAs($this->user)->get(OrderResource::getUrl('index'))
        ->assertSuccessful();

    $this->actingAs($this->user)->get(OrderResource::getUrl('create'))
        ->assertForbidden();

    $this->actingAs($this->user)->get(OrderResource::getUrl('edit', [
        'record' => $this->orders->first()->getKey()
    ]))->assertSuccessful();
})
->coversClass(OrderResource::class)
->group('order_resource_urls');

it('has the required fields', function () {

    $firstOrder = $this->orders->first();

    // @todo Unable to run this with `livewire(CreateOrder::class)...`. Possible bug with Filament. Investigate
    livewire(EditOrder::class, [
        'record' => $this->orders->first()->getKey()
    ])
        ->assertFormFieldExists('id', function(TextInput $field) {
            return $field->isDisabled() &&
                !$field->isDehydrated();
        })
        ->assertFormFieldExists('customer', function(Select $field) {
            return $field->isDisabled() &&
                !$field->isDehydrated() &&
                $field->hasRelationship() &&
                $field->getRelationshipName() == 'customer' &&
                $field->getRelationshipTitleAttribute() == 'name';
        })
        ->assertFormFieldExists('order_subtotal', function(TextInput $field) use ($firstOrder) {
            return $field->isDisabled() &&
                !$field->isDehydrated() &&
                $field->getState() == NumberFormatter::create('en_GB', NumberFormatter::CURRENCY)->formatCurrency($firstOrder->subtotal/100, 'GBP');
        })
        ->assertFormFieldExists('tax', function(TextInput $field) use ($firstOrder) {
            return $field->isDisabled() &&
                !$field->isDehydrated() &&
                $field->getState() == NumberFormatter::create('en_GB', NumberFormatter::CURRENCY)->formatCurrency($firstOrder->tax/100, 'GBP');
        })
        ->assertFormFieldExists('order_total', function(TextInput $field) use ($firstOrder) {
            return $field->isDisabled() &&
                !$field->isDehydrated() &&
                $field->getState() == NumberFormatter::create('en_GB', NumberFormatter::CURRENCY)->formatCurrency($firstOrder->total/100, 'GBP');
        })
        ->assertFormFieldExists('status', function(TextInput $field) use ($firstOrder) {
            return $field->isDisabled() &&
                !$field->isDehydrated() &&
                $field->getState() == $firstOrder->status;
        });
});

it('sets the state correctly', function () {

    livewire(EditOrder::class, [
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
->coversClass(OrderResource::class)
->group('order_resource_form_fields_all');

test('the id field is disabled', function () {

    livewire(EditOrder::class, [
        'record' => $this->orders->first()->id
    ])
    ->assertFormFieldIsDisabled('id');
})
->coversClass(OrderResource::class);

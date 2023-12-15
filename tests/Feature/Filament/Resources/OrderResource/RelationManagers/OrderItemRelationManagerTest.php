<?php

use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestUser;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Antidote\LaravelCartFilament\Resources\OrderResource\Pages\EditOrder;
use Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderItemRelationManager;
use function Pest\Livewire\livewire;

beforeEach(function() {

    CartPanelPlugin::set('models.product', TestProduct::class);

    $this->product = TestProduct::factory()->asSimpleProduct()->create([
        'description' => 'a very very simple product'
    ]);

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
        'password' => \Illuminate\Support\Facades\Hash::make('password')
    ]);

});

it('will display the order items', function() {

    livewire(OrderItemRelationManager::class, [
        'pageClass' => EditOrder::class,
        'ownerRecord' => $this->orders->first()
    ])
    ->assertCanSeeTableRecords($this->orders->first()->items);
})
->covers(OrderItemRelationManager::class);

it('will display the order item columns', function () {

    $first_order_item = $this->orders->first()->items()->first();

    livewire(OrderItemRelationManager::class, [
        'pageClass' => EditOrder::class,
        'ownerRecord' => $this->orders->first()
    ])
    ->assertTableColumnStateSet('product_name', $this->product->getName(), $first_order_item)
    ->assertTableColumnHasDescription('product_name', $this->product->getDescription(), $first_order_item)
    ->assertTableColumnStateSet('quantity', $first_order_item->quantity, $first_order_item)
    ->assertTableColumnStateSet('price', $first_order_item->product->getPrice(), $first_order_item)
    ->assertTableColumnStateSet('cost', $first_order_item->getCost(), $first_order_item);
})
->covers(OrderItemRelationManager::class);

<?php

beforeEach(function() {

    $this->product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct()->create();
    $this->customer = \Antidote\LaravelCart\Models\Customer::factory()->create();
    $this->orders = \Antidote\LaravelCart\Models\Order::factory()
        ->count(10)
        ->withProduct($this->product)
        ->forCustomer($this->customer)
        ->create([
            'status' => 'an order status'
        ]);

    $this->user = \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestUser::create([
        'name' => 'Test User',
        'email' => 'test@user.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password')
    ]);

});

it('will display the order items', function() {

    \Pest\Livewire\livewire(\Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderItemRelationManager::class, [
        'pageClass' => \Antidote\LaravelCartFilament\Resources\OrderResource\Pages\EditOrder::class,
        'ownerRecord' => $this->orders->first()
    ])
    ->assertCanSeeTableRecords($this->orders->first()->items);
})
->covers(\Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderItemRelationManager::class);

it('will display the order item columns', function () {

    $first_order_item = $this->orders->first()->items()->first();

    \Pest\Livewire\livewire(\Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderItemRelationManager::class, [
        'pageClass' => \Antidote\LaravelCartFilament\Resources\OrderResource\Pages\EditOrder::class,
        'ownerRecord' => $this->orders->first()
    ])
    ->assertTableColumnStateSet('product_name', $this->product->getName(), $first_order_item)
    ->assertTableColumnHasDescription('product_name', $this->product->getDescription($this->product->product_data), $first_order_item)
    ->assertTableColumnStateSet('quantity', $first_order_item->quantity, $first_order_item)
    ->assertTableColumnStateSet('price', $first_order_item->product->getPrice(), $first_order_item)
    ->assertTableColumnStateSet('cost', $first_order_item->getCost(), $first_order_item);
})
->covers(\Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderItemRelationManager::class);

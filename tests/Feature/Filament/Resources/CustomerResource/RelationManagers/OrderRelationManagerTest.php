<?php

beforeEach(function() {

    $this->product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()
        ->asSimpleProduct(['price' => 1000])
        ->create();

    $this->customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $this->orders = \Antidote\LaravelCart\Models\Order::factory(10)
        ->withProduct($this->product, rand(1,3))
        ->forCustomer($this->customer)
        ->create();
});

it('will list the orders', function() {

    \Pest\Livewire\livewire(\Antidote\LaravelCartFilament\Resources\CustomerResource\RelationManagers\OrderRelationManager::class, [
        'ownerRecord' => $this->customer
    ])
    ->assertCanSeeTableRecords(\Antidote\LaravelCart\Models\Order::all());
})
->covers(\Antidote\LaravelCartFilament\Resources\CustomerResource\RelationManagers\OrderRelationManager::class);

it('has the correct columns', function () {

    \Pest\Livewire\livewire(\Antidote\LaravelCartFilament\Resources\CustomerResource\RelationManagers\OrderRelationManager::class, [
        'ownerRecord' => $this->customer
    ])
    ->assertTableColumnStateSet('id', $this->orders->first()->id, $this->orders->first())
    ->assertTableColumnStateSet('total', $this->orders->first()->total, $this->orders->first());
})
->covers(\Antidote\LaravelCartFilament\Resources\CustomerResource\RelationManagers\OrderRelationManager::class);

it('will navigate to the order when a row is clicked', function () {

    $this->markTestIncomplete('cannot check this as `getTableRecordUrlUsing` is protected - looks like this has been opened up in v3');

    \Pest\Livewire\livewire(\Antidote\LaravelCartFilament\Resources\CustomerResource\RelationManagers\OrderRelationManager::class, [
        'ownerRecord' => $this->customer
    ])
    ->assertTableHasRecordUrl(\Antidote\LaravelCartFilament\Resources\OrderResource::getUrl('edit', ['record' => $this->orders->first()]), $this->orders->first());
});

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

    //@todo make factory
    $order_log_item = \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrderLogItem::create([
        'message' => 'This is an order log item',
        'order_id' => $this->orders->first()->id
    ]);

    $order_log_item->event = [
        'field_one' => 'value_one',
        'field_two' => 'value-two'
    ];

    $order_log_item->save();

});

it('will display the order log items', function() {

    \Pest\Livewire\livewire(\Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderLogItemRelationManager::class, [
        'ownerRecord' => $this->orders->first()
    ])
    ->assertCanSeeTableRecords($this->orders->first()->logitems);
})
->covers(\Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderLogItemRelationManager::class);

it('will display the order log items columns', function() {

    $first_order_log_item = $this->orders->first()->logitems()->first();

    \Pest\Livewire\livewire(\Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderLogItemRelationManager::class, [
        'ownerRecord' => $this->orders->first()
    ])
    ->assertTableColumnStateSet('created_at', $first_order_log_item->created_at, $first_order_log_item)
    ->assertTableColumnStateSet('message', $first_order_log_item->message, $first_order_log_item);
})
->covers(\Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderLogItemRelationManager::class);

it('will provide an action to view stripe event if stripe order log item is used', function () {

    \Pest\Livewire\livewire(\Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderLogItemRelationManager::class, [
        'ownerRecord' => $this->orders->first()
    ])
    ->assertTableActionExists('event');

    class T extends \Antidote\LaravelCart\Models\OrderLogItem {}

    $config = app('config');
    $config->set('laravel-cart.classes.order_log_item', T::class);

    \Pest\Livewire\livewire(\Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderLogItemRelationManager::class, [
        'ownerRecord' => $this->orders->first()
    ])
    ->assertTableActionDoesNotExist('event');
});

it('will display the stripe event', function () {

    //$this->markTestIncomplete('need assertion to test view and data from action - @link https://github.com/filamentphp/filament/discussions/8048');

    //$config = app('config');
    //$config->set('laravel-cart.classes.order_log_item', \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrderLogItem::class);

    \Pest\Livewire\livewire(\Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderLogItemRelationManager::class, [
        'ownerRecord' => $this->orders->first()
    ])
    ->assertTableActionExists('event')
    ->assertTableActionHasModalContentViewName('event', 'laravel-cart-filament::stripe-event', $this->orders->first()->logitems()->first())
    ->assertTableActionHasModalContentViewData('event', [
         'event_data' => $this->orders->first()->logitems()->first()->event
    ], $this->orders->first()->logitems()->first());
});

test('the event modal view correctly returns the formatted data', function () {

    $this->markTestSkipped('Test fails to due to error in `callTableAction` - @see https://github.com/filamentphp/filament/discussions/8048');

    dump($this->orders->first()->logItems->first()->attributesToArray());

    \Pest\Livewire\livewire(\Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderLogItemRelationManager::class, [
        'ownerRecord' => $this->orders->first()
    ])
    ->callTableAction('event', $this->orders->first()->logitems()->first())
    ->assertSeeHtml(<<<EOF
<table class='text-xs'>
    <tr>
        <td class='p-1'>field_one</td>
        <td class='p-1'>
            'value_one'
        </td>
    </tr>
    <tr>
        <td class='p-1'>field_two</td>
        <td class='p-1'>
            'value_two'
        </td>
    </tr>
</table>
EOF);

});

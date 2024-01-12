<?php

use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Models\OrderLogItem;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrderLogItem;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestUser;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Antidote\LaravelCartFilament\Resources\OrderResource\Pages\EditOrder;
use Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderLogItemRelationManager;
use function Pest\Livewire\livewire;

beforeEach(function() {

    CartPanelPlugin::make()->config([
        'models' => [
            'product' => TestProduct::class,
            'order_log_item' => TestStripeOrderLogItem::class
        ]
    ]);

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
        'password' => \Illuminate\Support\Facades\Hash::make('password')
    ]);

    //@todo make factory
    $order_log_item = OrderLogItem::create([
        'message' => 'This is an order log item',
        'order_id' => $this->orders->first()->id
    ]);

//    $order_log_item->event = [
//        'field_one' => 'value_one',
//        'field_two' => 'value-two'
//    ];

    $order_log_item->save();

});

it('will display the order log items', function() {

    livewire(OrderLogItemRelationManager::class, [
        'pageClass' => EditOrder::class,
        'ownerRecord' => $this->orders->first()
    ])
    ->assertCanSeeTableRecords($this->orders->first()->logitems);
})
->covers(OrderLogItemRelationManager::class);

it('will display the order log items columns', function() {

    $first_order_log_item = $this->orders->first()->logitems()->first();

    livewire(OrderLogItemRelationManager::class, [
        'pageClass' => EditOrder::class,
        'ownerRecord' => $this->orders->first()
    ])
    ->assertTableColumnStateSet('created_at', $first_order_log_item->created_at, $first_order_log_item)
    ->assertTableColumnStateSet('message', $first_order_log_item->message, $first_order_log_item);
})
->covers(OrderLogItemRelationManager::class);

it('will provide an action to view stripe event if stripe order log item is used', function () {

    livewire(OrderLogItemRelationManager::class, [
        'pageClass' => EditOrder::class,
        'ownerRecord' => $this->orders->first()
    ])
    ->assertTableActionExists('event');

    CartPanelPlugin::set('models.order_log_item', OrderLogItem::class);

    livewire(OrderLogItemRelationManager::class, [
        'pageClass' => EditOrder::class,
        'ownerRecord' => $this->orders->first()
    ])
    ->assertTableActionDoesNotExist('event');
});

it('will display the stripe event', function () {

    $this->markTestSkipped('event no longer logged');

    livewire(OrderLogItemRelationManager::class, [
        'pageClass' => EditOrder::class,
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

    livewire(OrderLogItemRelationManager::class, [
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

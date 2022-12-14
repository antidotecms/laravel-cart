<?php

use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrder;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrderLogItem;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestUser;
use function Pest\Livewire\livewire;

it('will list the orders', function () {

    Config::set('laravel-cart.classes.order', TestOrder::class);
    Config::set('laravel-cart.classes.order_log_item', TestOrderLogItem::class);
    Config::set('laravel-cart.stripe.log', false);

    $product = TestProduct::factory()->asSimpleProduct()->create();
    $customer = TestCustomer::factory()->create();
    $orders = TestOrder::factory()
        ->count(10)
        ->withProduct($product)
        ->forCustomer($customer)
        ->create();

    $user = TestUser::create([
        'name' => 'Test User',
        'email' => 'test@user.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password')
    ]);

    $this->actingAs($user)->get(\Antidote\LaravelCartFilament\Resources\OrderResource::getUrl('index'))->assertSuccessful();

    livewire(\Antidote\LaravelCartFilament\Resources\OrderResource\Pages\ListOrders::class)
        ->assertCanSeeTableRecords($orders);
});

it('can render the edit page', function () {

    Config::set('laravel-cart.classes.order', TestOrder::class);
    Config::set('laravel-cart.classes.order_log_item', TestOrderLogItem::class);
    Config::set('laravel-cart.stripe.log', false);

    $product = TestProduct::factory()->asSimpleProduct()->create();
    $customer = TestCustomer::factory()->create();
    $order = TestOrder::factory()
        ->withProduct($product)
        ->forCustomer($customer)
        ->create();

    $user = TestUser::create([
        'name' => 'Test User',
        'email' => 'test@user.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password')
    ]);

    $response = $this->actingAs($user)->get(\Antidote\LaravelCartFilament\Resources\OrderResource::getUrl('edit', [
        'record' => $order->getKey()
    ]))->assertSuccessful();

    //dump($response);
});

it('will allow sending an order confirmation mail again', function() {

    Config::set('laravel-cart.classes.order', TestOrder::class);
    Config::set('laravel-cart.classes.order_log_item', TestOrderLogItem::class);
    Config::set('laravel-cart.stripe.log', false);

    $product = TestProduct::factory()->asSimpleProduct()->create();
    $customer = TestCustomer::factory()->create();
    $order = TestOrder::factory()
        ->withProduct($product)
        ->forCustomer($customer)
        ->create();

    \Illuminate\Support\Facades\Event::fake();

    livewire(\Antidote\LaravelCartFilament\Resources\OrderResource\Pages\EditOrder::class, [
            'record' => $order->getKey()
        ])
        ->callPageAction('resend_order_complete_notification');

    \Illuminate\Support\Facades\Event::assertDispatched(\Antidote\LaravelCart\Events\OrderCompleted::class);

    //expect($order->logitems->count())->toBe(1);
});

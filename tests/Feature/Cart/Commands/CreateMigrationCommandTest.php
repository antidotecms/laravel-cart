<?php

use Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrder;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Mockery\MockInterface;

it('will create the payments migration', function() {

    $contents = file_get_contents('../../../packages/antidote/laravel-cart/packages/cart/stubs/migrations/create-payments-table.stub');
    $contents = str_replace('$order_key$', getKeyFor('order'), $contents);
    $contents = str_replace('$table_name$', getTableNameFor('payment'), $contents);

    $date = Carbon::now()->format('Y_m_d_Gis');
    $filename =  $date . '_create_test_payments_table.php';

    $this->mock(Filesystem::class, function (MockInterface $mock) use ($filename, $contents) {
        $mock->shouldReceive('put')
            ->once()
            ->withArgs([
                database_path('migrations').'/'.$filename,
                $contents
            ])
            ->andReturnTrue();
    });

    $this->artisan('cart:migration --type=payment')
        ->expectsOutput("Migration $filename created.")
        ->assertExitCode(0);
});

it('will create the stripe payments migration', function() {

    Config::set('laravel-cart.classes.payment', \Antidote\LaravelCartStripe\Models\StripePayment::class);
    Config::set('laravel-cart.classes.order', TestStripeOrder::class);

    $contents = file_get_contents('../../../packages/antidote/laravel-cart/packages/cart/stubs/migrations/create-payments-table.stub');
    $contents = str_replace('$order_key$', getKeyFor('order'), $contents);
    $contents = str_replace('$table_name$', getTableNameFor('payment'), $contents);

    $date = Carbon::now()->format('Y_m_d_Gis');
    $filename =  $date . '_create_stripe_payments_table.php';

    $this->mock(Filesystem::class, function (MockInterface $mock) use ($filename, $contents) {
        $mock->shouldReceive('put')
            ->once()
            ->withArgs([
                database_path('migrations').'/'.$filename,
                $contents
            ])
            ->andReturnTrue();
    });

    $this->artisan('cart:migration --type=payment')
        ->expectsOutput("Migration $filename created.")
        ->assertExitCode(0);
});

it('will create the order adjustment table', function () {

    $contents = file_get_contents('../../../packages/antidote/laravel-cart/packages/cart/stubs/migrations/create-order-adjustments-table.stub');
    $contents = str_replace('$order_key$', getKeyFor('order'), $contents);
    $contents = str_replace('$table_name$', getTableNameFor('order_adjustment'), $contents);

    $date = Carbon::now()->format('Y_m_d_Gis');
    $filename =  $date . '_create_test_order_adjustments_table.php';

    $this->mock(Filesystem::class, function (MockInterface $mock) use ($filename, $contents) {
        $mock->shouldReceive('put')
            ->once()
            ->withArgs([
                database_path('migrations').'/'.$filename,
                $contents
            ])
            ->andReturnTrue();
    });

    $this->artisan('cart:migration --type=order_adjustment')
        ->expectsOutput("Migration $filename created.")
        ->assertExitCode(0);

});

it('will create the orders table', function () {

    $contents = file_get_contents('../../../packages/antidote/laravel-cart/packages/cart/stubs/migrations/create-orders-table.stub');
    $contents = str_replace('$customer_key$', getKeyFor('customer'), $contents);
    $contents = str_replace('$table_name$', getTableNameFor('order'), $contents);

    $date = Carbon::now()->format('Y_m_d_Gis');
    $filename =  $date . '_create_test_orders_table.php';

    $this->mock(Filesystem::class, function (MockInterface $mock) use ($filename, $contents) {
        $mock->shouldReceive('put')
            ->once()
            ->withArgs([
                database_path('migrations').'/'.$filename,
                $contents
            ])
            ->andReturnTrue();
    });

    $this->artisan('cart:migration --type=order')
        ->expectsOutput("Migration $filename created.")
        ->assertExitCode(0);

});

it('will create the order items table', function () {

    $contents = file_get_contents('../../../packages/antidote/laravel-cart/packages/cart/stubs/migrations/create-order-items-table.stub');
    $contents = str_replace('$order_key$', getKeyFor('order'), $contents);
    $contents = str_replace('$product_key$', getKeyFor('product'), $contents);
    $contents = str_replace('$table_name$', getTableNameFor('order_item'), $contents);

    $date = Carbon::now()->format('Y_m_d_Gis');
    $filename =  $date . '_create_test_order_items_table.php';

    $this->mock(Filesystem::class, function (MockInterface $mock) use ($filename, $contents) {
        $mock->shouldReceive('put')
            ->once()
            ->withArgs([
                database_path('migrations').'/'.$filename,
                $contents
            ])
            ->andReturnTrue();
    });

    $this->artisan('cart:migration --type=order_item')
        ->expectsOutput("Migration $filename created.")
        ->assertExitCode(0);

});

it('will create the order log items table', function () {

    $contents = file_get_contents('../../../packages/antidote/laravel-cart/packages/cart/stubs/migrations/create-order-log-items-table.stub');
    $contents = str_replace('$order_key$', getKeyFor('order'), $contents);
    $contents = str_replace('$table_name$', getTableNameFor('order_log_item'), $contents);

    $date = Carbon::now()->format('Y_m_d_Gis');
    $filename =  $date . '_create_test_order_log_items_table.php';

    $this->mock(Filesystem::class, function (MockInterface $mock) use ($filename, $contents) {
        $mock->shouldReceive('put')
            ->once()
            ->withArgs([
                database_path('migrations').'/'.$filename,
                $contents
            ])
            ->andReturnTrue();
    });

    $this->artisan('cart:migration --type=order_log_item')
        ->expectsOutput("Migration $filename created.")
        ->assertExitCode(0);

});

it('will create the customers table', function () {

    $contents = file_get_contents('../../../packages/antidote/laravel-cart/packages/cart/stubs/migrations/create-customers-table.stub');
    $contents = str_replace('$table_name$', getTableNameFor('customer'), $contents);

    $date = Carbon::now()->format('Y_m_d_Gis');
    $filename =  $date . '_create_test_customers_table.php';

    $this->mock(Filesystem::class, function (MockInterface $mock) use ($filename, $contents) {
        $mock->shouldReceive('put')
            ->once()
            ->withArgs([
                database_path('migrations').'/'.$filename,
                $contents
            ])
            ->andReturnTrue();
    });

    $this->artisan('cart:migration --type=customer')
        ->expectsOutput("Migration $filename created.")
        ->assertExitCode(0);
});

it('will create the products table', function () {

    $contents = file_get_contents('../../../packages/antidote/laravel-cart/packages/cart/stubs/migrations/create-products-table.stub');
    $contents = str_replace('$table_name$', getTableNameFor('product'), $contents);

    $date = Carbon::now()->format('Y_m_d_Gis');
    $filename =  $date . '_create_test_products_table.php';

    $this->mock(Filesystem::class, function (MockInterface $mock) use ($filename, $contents) {
        $mock->shouldReceive('put')
            ->once()
            ->withArgs([
                database_path('migrations').'/'.$filename,
                $contents
            ])
            ->andReturnTrue();
    });

    $this->artisan('cart:migration --type=product')
        ->expectsOutput("Migration $filename created.")
        ->assertExitCode(0);
});

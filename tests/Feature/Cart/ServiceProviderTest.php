<?php

namespace Tests\Feature\Cart;

use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestAdjustment;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestCustomer;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrder;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderAdjustment;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderItem;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderLogItem;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

class ServiceProviderTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    //@todo define env for stripe
    protected function defineEnvironment($app)
    {
        $app->config->set('laravel-cart.classes.product', TestProduct::class);
        $app->config->set('laravel-cart.classes.customer', TestCustomer::class);
        $app->config->set('laravel-cart.classes.order', TestOrder::class);
        $app->config->set('laravel-cart.classes.order_item', TestOrderItem::class);
        $app->config->set('laravel-cart.classes.order_adjustment', TestOrderAdjustment::class);
        $app->config->set('laravel-cart.classes.adjustment', TestAdjustment::class);
        $app->config->set('laravel-cart.classes.payment', TestPayment::class);
        $app->config->set('laravel-cart.classes.order_log_item', TestOrderLogItem::class);
    }

    protected function getPackageProviders($app)
    {
        return [
            \Antidote\LaravelCart\ServiceProvider::class
        ];
    }

    protected function dataProviderColumns()
    {
        return [
            'products' => [
                'table' => 'test_products',
                'included' => [
                    'id',
                    'name',
                    'description',
                    'product_type_id',
                    'product_type_type',
                    'created_at',
                    'updated_at',
                    'deleted_at'
                ]
            ],

            'order' => [
                'table' => 'test_orders',
                'included' => [
                    'id',
                    'status',
                    'test_customer_id',
                    'payment_id',
                    'payment_type',
                    //@todo below field only used for testing
                    'additional_field',
                    'created_at',
                    'updated_at',
                    'deleted_at'
                ],
                //@todo run with StripeOrderLogItem
                'excluded' => [
                    'payment_intent_id'
                ]
            ],

            'order_items' => [
                'table' => 'test_order_items',
                'included' => [
                    'id',
                    'name',
                    'test_product_id',
                    'product_data',
                    'price',
                    'quantity',
                    'test_order_id',
                    'created_at',
                    'updated_at'
                ],
                'excluded' => [

                ]
            ],

            'order log items' => [
                'table' => 'test_order_log_items',
                'included' => [
                    'id',
                    'message',
                    'test_order_id',
                    'created_at',
                    'updated_at',
                    //no soft deleting of order log items
                    //'deleted_at'
                ],
                //@todo run with StripeOrderLogItem
                'excluded' => [
                    'event'
                ]
            ],

            'order_adjustments' => [
                'table' => 'test_order_adjustments',
                'included' => [
                    'id',
                    'name',
                    'amount',
                    'original_parameters',
                    'apply_to_subtotal',
                    'test_order_id',
                    'class',
                    'created_at',
                    'updated_at'
                ],
                'excluded' => []
            ],

            'adjustments' => [
                'table' => 'test_adjustments',
                'included' => [
                    'id',
                    'name',
                    'class',
                    'parameters',
                    'apply_to_subtotal',
                    'is_active',
                    'is_valid',
                    'calculated_amount',
                    'created_at',
                    'updated_at'
                ]
            ],

            'customers' => [
                'table' => 'test_customers',
                'included' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'password',
                    //@todo address not needed for prod but is used in tests
                    'address',
                    'remember_token',
                    'created_at',
                    'updated_at'
                ]
            ],
        ];
    }

    /**
     * @test
     */
    public function it_creates_all_the_necessary_tables()
    {
        $tables = collect($this->dataProviderColumns())->pluck('table');

        foreach($tables as $table) {
            $this->assertTrue(Schema::hasTable($table));
        }
    }

    /**
     * @test
     * @dataProvider dataProviderColumns
     */
    public function it_will_correctly_create_the_tables($table, $included = [], $excluded = [])
    {
        $this->assertTrue(Schema::hasTable($table));

        foreach($included as $include) {
            $this->assertTrue(Schema::hasColumn($table, $include), "Table $table does not have column $include");
        }

        foreach($excluded as $exclude) {
            $this->assertFalse(Schema::hasColumn($table, $exclude), "Table $table has column $include");
        }

        $this->assertEqualsCanonicalizing($included, Schema::getColumnListing($table));



        //TestOrderLogItem does not extend StripeorderLogItem
        //$this->assertFalse(Schema::hasColumn('test_order_log_items', 'event'));
    }
}

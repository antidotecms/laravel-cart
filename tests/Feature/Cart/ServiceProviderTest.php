<?php

namespace Tests\Feature\Cart;

use Antidote\LaravelCart\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/**
 * @covers \Antidote\LaravelCart\CartServiceProvider
 */
class ServiceProviderTest extends TestCase
{
    use RefreshDatabase;

//    protected function getPackageProviders($app)
//    {
//        return [
//            \Antidote\LaravelCart\CartServiceProvider::class
//        ];
//    }

    public function dataProviderColumns()
    {
        return $this->getColumns();
    }

    protected function getColumns($merge = [])
    {
        return array_merge([
            'products' => [
                'table' => 'products',
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
                'table' => 'orders',
                'included' => [
                    'id',
                    'status',
                    'customer_id',
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
                'table' => 'order_items',
                'included' => [
                    'id',
                    'name',
                    'product_id',
                    'product_data',
                    'price',
                    'quantity',
                    'order_id',
                    'created_at',
                    'updated_at'
                ],
                'excluded' => [

                ]
            ],

            'order log items' => [
                'table' => 'order_log_items',
                'included' => [
                    'id',
                    'event',
                    'message',
                    'order_id',
                    'created_at',
                    'updated_at'
                ],
                'excluded' => []
            ],

            'order_adjustments' => [
                'table' => 'order_adjustments',
                'included' => [
                    'id',
                    'name',
                    'amount',
                    'original_parameters',
                    'apply_to_subtotal',
                    'order_id',
                    'class',
                    'created_at',
                    'updated_at'
                ],
                'excluded' => []
            ],

            'adjustments' => [
                'table' => 'adjustments',
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
                'table' => 'customers',
                'included' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'password',
                    'remember_token',
                    'created_at',
                    'updated_at'
                ]
            ],
        ],
        $merge);
    }

    /**
     * @test
     * @dataProvider dataProviderColumns
     */
    public function it_will_correctly_create_the_tables($table, $included = [], $excluded = [])
    {
        $this->assertDatabaseStructure($table, $included, $excluded);
    }

    //@todo make this an assertion or break up into a collection of assertions (assertTableHas, assertTableDoesNotHave etc)
    private function assertDatabaseStructure($table, $included = [], $excluded = [])
    {
        $this->assertTrue(Schema::hasTable($table));

        foreach($included as $include) {
            $this->assertTrue(Schema::hasColumn($table, $include), "Failed asserting that table $table has column $include");
        }

        foreach($excluded as $exclude) {
            $this->assertFalse(Schema::hasColumn($table, $exclude), "Failed asserting that table $table does not have column $exclude");
        }

        $this->assertEqualsCanonicalizing($included, Schema::getColumnListing($table));
    }

    /**
     * @test
     */
    public function it_has_the_correct_routes()
    {
        expect(Route::has('laravel-cart.order_complete'))->toBeTrue();
        expect(Route::has('laravel-cart.replace_cart'))->toBeTrue();
        expect(Route::has('laravel-cart.add_to_cart'))->toBeTrue();
    }

}

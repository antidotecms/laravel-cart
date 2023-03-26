<?php

namespace Tests\Feature\Cart;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

/**
 * @covers \Antidote\LaravelCart\ServiceProvider
 */
class ServiceProviderTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app)
    {
        return [
            \Antidote\LaravelCart\ServiceProvider::class
        ];
    }

    protected function dataProviderColumns()
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
}

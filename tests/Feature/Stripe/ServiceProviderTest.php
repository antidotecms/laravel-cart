<?php

namespace Antidote\LaravelCart\Tests\Feature\Stripe;

use Antidote\LaravelCartStripe\ServiceProvider;
use Illuminate\Support\Arr;

/**
 * @covers \Antidote\LaravelCartStripe\ServiceProvider
 */
class ServiceProviderTest extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class
        ];
    }

    protected function defineEnv($app)
    {
        $app->config->set('laravel-cart.urls.order_complete', '/order/complete');
    }

    /**
     * @test
     */
    public function it_will_merge_arrays()
    {
        $array1 = [
            'name' => 'Alice',
            'age' => 27,
            'country' => 'United Kingdom',
            'event' => [
                'type' => 'party',
                'when' => 'tomorrow'
            ],
            'another_event' => [
                'type' => 'work',
                'when' => 'today'
            ]
        ];

        $array2 = [
            'age' => 32,
            'event' => [
                'type' => 'party',
                'when' => 'in 2 days time'
            ]
        ];

        $new_array = Arr::mergeDeep($array1, $array2);

        expect($new_array)->toEqualCanonicalizing([
            'name' => 'Alice',
            'age' => 32,
            'country' => 'United Kingdom',
            'event' => [
                'type' => 'party',
                'when' => 'in 2 days time'
            ],
            'another_event' => [
                'type' => 'work',
                'when' => 'today'
            ]
        ]);
    }
}

<?php

namespace Antidote\LaravelCart\Tests\Feature\Stripe\Http\Controllers;

use Antidote\LaravelCartStripe\ServiceProvider;
use Illuminate\Support\Arr;

class ServiceProviderTest extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class
        ];
    }

    /**
     * @test
     */
    public function it_will_merge_arrays()
    {
        $array1 = [
            'name' => 'Alice',
            'age' => 27,
            'country' => 'United Kingdom'
        ];

        $array2 = [
            'age' => 32
        ];

        $new_array = Arr::mergeDeep($array1, $array2);

        expect($new_array)->toEqualCanonicalizing([
            'name' => 'Alice',
            'age' => 32,
            'country' => 'United Kingdom'
        ]);
    }
}

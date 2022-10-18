<?php

use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestCustomer;

uses(\Antidote\LaravelCart\Tests\TestCase::class)->in('Feature/Cart');
uses(\Antidote\LaravelCart\Tests\StripeTestCase::class)->in('Feature/Stripe');
uses(\Antidote\LaravelCart\Tests\BrowserTestCase::class)->in('Browser');

//uses(RefreshDatabase::class)->in('Feature');

function actingAsCustomer(TestCustomer $customer, string $driver = null)
{
    return test()->actingAs($customer, $driver);
}

function createStripeEvent(string $type, array $parameters = [])
{
    $event = match($type) {
        'payment_intent.created' => include 'Fixtures/Stripe/events/payment_intent.created.php'
    };

    return arraysMergeUnique($event, $parameters);
}

//https://stackoverflow.com/a/20550845
function arraysMergeUnique($array1, $array2)
{
    foreach ($array2 as $k => $v) {
        if ( is_array($array1) ) {
            if ( is_string($v) && ! in_array($v, $array1) ) {
                /**
                 *  Preserve keys in n-dimension using $k
                 */
                $array1[$k] = $v;
            } else if ( is_array($v) ) {
                if ( isset($array1[$k]) ) {
                    $array1[$k] = arraysMergeUnique($array1[$k], $v);
                } else {
                    $array1[$k] = $v;
                }
            }
        } else {
            $array1 = array($v);
        }
    }
    return $array1;
}

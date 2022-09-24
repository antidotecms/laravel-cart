<?php

use Antidote\LaravelCart\Tests\Fixtures\cart\Models\Products\TestCustomer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(\Antidote\LaravelCart\Tests\TestCase::class)->in(__DIR__);

uses(RefreshDatabase::class)->in('Feature');

function actingAsCustomer(TestCustomer $customer, string $driver = null)
{
    return test()->actingAs($customer, $driver);
}

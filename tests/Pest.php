<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fixtures\app\Models\Products\TestCustomer;

uses(\Tests\TestCase::class)->in(__DIR__);

uses(RefreshDatabase::class)->in('Feature');

function actingAsCustomer(TestCustomer $customer, string $driver = null)
{
    return test()->actingAs($customer, $driver);
}

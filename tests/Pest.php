<?php

use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestCustomer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(\Antidote\LaravelCart\Tests\TestCase::class)->in('Feature');
uses(\Antidote\LaravelCart\Tests\BrowserTestCase::class)->in('Browser');

uses(RefreshDatabase::class)->in('Feature');

function actingAsCustomer(TestCustomer $customer, string $driver = null)
{
    return test()->actingAs($customer, $driver);
}

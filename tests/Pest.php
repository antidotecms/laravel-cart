<?php

use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestCustomer;

uses(\Antidote\LaravelCart\Tests\TestCase::class)->in('Feature/Cart/Database/Factories');
uses(\Antidote\LaravelCart\Tests\TestCase::class)->in('Feature/Cart/Models');
uses(\Antidote\LaravelCart\Tests\TestCase::class)->in('Feature/Cart/Http/Controllers');
uses(\Antidote\LaravelCart\Tests\TestCase::class)->in('Feature/Cart/Commands');
uses(\Antidote\LaravelCart\Tests\TestCase::class)->in('Feature/Cart/Mail');
uses(\Antidote\LaravelCart\Tests\TestCase::class)->in('Feature/Filament');
uses(\Antidote\LaravelCart\Tests\TestCase::class)->in('Feature/Stripe/Http/Middleware');
//uses(\Antidote\LaravelCart\Tests\TestCase::class)->in('Feature/Filament');
//uses(\Antidote\LaravelCart\Tests\StripeTestCase::class)->in('Feature/Stripe/Http/Controllers');
//uses(\Antidote\LaravelCart\Tests\BrowserTestCase::class)->in('Browser');

//uses(RefreshDatabase::class)->in('Feature');

function actingAsCustomer(TestCustomer $customer, string $driver = null)
{
    return test()->actingAs($customer, $driver);
}





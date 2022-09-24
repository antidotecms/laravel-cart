<?php

namespace Antidote\LaravelCart\Tests\Fixtures\cart\Models\Products;

use Antidote\LaravelCart\Contracts\Customer;
use Antidote\LaravelCart\Tests\Fixtures\database\factories\Products\TestCustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestCustomer extends Customer
{
    use HasFactory;

    protected static function newFactory()
    {
        return TestCustomerFactory::new();
    }
}

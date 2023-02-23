<?php

namespace Antidote\LaravelCart\Tests\Fixtures\App\Models;

use Antidote\LaravelCart\Contracts\Customer;
use Antidote\LaravelCart\Tests\Fixtures\factories\Products\TestCustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestCustomer extends Customer
{
    use HasFactory;

    protected static function newFactory()
    {
        return TestCustomerFactory::new();
    }
}

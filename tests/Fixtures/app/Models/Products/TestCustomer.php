<?php

namespace Tests\Fixtures\app\Models\Products;

use Antidote\LaravelCart\Models\Customer;
use Database\Factories\Tests\Fixtures\app\Models\Products\TestCustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestCustomer extends Customer
{
    use HasFactory;

    protected static function newFactory()
    {
        return TestCustomerFactory::new();
    }
}

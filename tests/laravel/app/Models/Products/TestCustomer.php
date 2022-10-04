<?php

namespace Antidote\LaravelCart\Tests\laravel\app\Models\Products;

use Antidote\LaravelCart\Contracts\Customer;
use Antidote\LaravelCart\Tests\laravel\database\factories\Products\TestCustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestCustomer extends Customer
{
    use HasFactory;

    protected static function newFactory()
    {
        return TestCustomerFactory::new();
    }
}

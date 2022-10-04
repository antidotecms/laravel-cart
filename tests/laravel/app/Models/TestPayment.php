<?php

namespace Antidote\LaravelCart\Tests\laravel\app\Models;

use Antidote\LaravelCart\Contracts\Payment;
use Antidote\LaravelCart\Tests\laravel\database\factories\TestPaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestPayment extends Payment
{
    use HasFactory;

    protected static function newFactory()
    {
        return TestPaymentFactory::new();
    }

    public function initialize(): void
    {
        // TODO: Implement initialize() method.
    }
}

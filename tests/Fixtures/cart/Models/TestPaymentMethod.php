<?php

namespace Antidote\LaravelCart\Tests\Fixtures\cart\Models;

use Antidote\LaravelCart\Contracts\PaymentMethod;
use Antidote\LaravelCart\Tests\Fixtures\database\factories\TestPaymentMethodFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestPaymentMethod extends PaymentMethod
{
    use HasFactory;

    protected static function newFactory()
    {
        return TestPaymentMethodFactory::new();
    }

    public function initialize(): void
    {
        // TODO: Implement initialize() method.
    }
}

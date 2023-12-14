<?php

namespace Antidote\LaravelCart\Tests\Fixtures\App\Models;

use Antidote\LaravelCart\Models\Order;

class TestOrder extends Order
{
    public function updateStatus()
    {
        return null;
    }
}

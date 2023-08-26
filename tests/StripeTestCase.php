<?php

namespace Antidote\LaravelCart\Tests;

use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestPayment;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrderLogItem;

class StripeTestCase extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->config->set('laravel-cart.classes.order_log_item', TestStripeOrderLogItem::class);
    }
}

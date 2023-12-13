<?php

namespace Antidote\LaravelCartStripe\Models;

class StripeOrderLogItem extends \Antidote\LaravelCartStripe\Contracts\StripeOrderLogItem
{
    public function getTable()
    {
        return 'order_log_items';
    }
}

<?php

namespace Antidote\LaravelCartStripe\Contracts;

use Antidote\LaravelCartStripe\Concerns\ConfiguresStripeOrderLogItem;

abstract class StripeOrderLogItem extends \Antidote\LaravelCart\Models\OrderLogItem
{
    use ConfiguresStripeOrderLogItem;
}

<?php

namespace Antidote\LaravelCartStripe\Contracts;

use Antidote\LaravelCartStripe\Concerns\ConfiguresStripeOrderLogItem;

abstract class StripeOrderLogItem extends \Antidote\LaravelCart\Contracts\OrderLogItem
{
    use ConfiguresStripeOrderLogItem;
}

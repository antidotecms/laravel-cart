<?php

namespace Antidote\LaravelCartStripe\Models;

use Antidote\LaravelCartStripe\Concerns\ConfiguresStripeOrderLogItem;

class StripeOrderLogItem extends \Antidote\LaravelCart\Contracts\OrderLogItem
{
    use ConfiguresStripeOrderLogItem;
}

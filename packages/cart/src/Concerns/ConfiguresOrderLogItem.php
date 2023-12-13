<?php

namespace Antidote\LaravelCart\Concerns;

use Antidote\LaravelCartStripe\Contracts\StripeOrderLogItem;

trait ConfiguresOrderLogItem
{
    public function getTable()
    {
        return 'order_log_items';
    }

    public function initializeConfiguresOrderLogItem() : void
    {
        $this->fillable[] = 'message';
        $this->fillable[] = 'order_id';

        if(static::class == StripeOrderLogItem::class){
            $this->fillable[] = 'event';
        }
    }
}

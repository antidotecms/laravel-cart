<?php

namespace Antidote\LaravelCart\Concerns;

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
    }
}

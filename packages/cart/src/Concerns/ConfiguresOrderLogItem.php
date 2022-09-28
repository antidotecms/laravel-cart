<?php

namespace Antidote\LaravelCart\Concerns;

trait ConfiguresOrderLogItem
{
    public function initializeConfiguresOrderLogItem() : void
    {
        $this->fillable[] = 'message';
        $this->fillable[] = getKeyFor('order');
    }
}

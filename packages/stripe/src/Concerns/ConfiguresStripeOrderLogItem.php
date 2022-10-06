<?php

namespace Antidote\LaravelCartStripe\Concerns;

trait ConfiguresStripeOrderLogItem
{
    public function initializeConfiguresStripeOrderLogItem() : void
    {
        $this->fillable[] = 'event';
    }

    public function getCasts() : array
    {
        return array_merge(parent::getCasts(), [
            'event' => 'array'
        ]);
    }
}

<?php

namespace Antidote\LaravelCart\Concerns;

trait ConfiguresOrderAdjustment
{
    public function initializeConfiguresOrderAdjustment() : void
    {
        $this->fillable[] = 'name';
        $this->fillable[] = 'class';
        $this->fillable[] = 'parameters';
        $this->fillable[] = getKeyFor('order');
    }

    public function getCasts() : array
    {
        return array_merge(parent::getCasts(), [
            'parameters' => 'array'
        ]);
    }
}

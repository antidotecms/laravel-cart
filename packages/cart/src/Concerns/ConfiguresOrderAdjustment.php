<?php

namespace Antidote\LaravelCart\Concerns;

trait ConfiguresOrderAdjustment
{
    public function initializeConfiguresOrderAdjustment() : void
    {
        $this->fillable[] = 'name';
        $this->fillable[] = getKeyFor('adjustment');
        $this->fillable[] = getKeyFor('order');
        $this->fillable[] = 'amount';
        $this->fillable[] = 'original_parameters';
    }

    public function getCasts() : array
    {
        return array_merge(parent::getCasts(), [
            'original_parameters' => 'array'
        ]);
    }

    public final function isValid()
    {
        return $this->adjustment->isValid();
    }

    public final function isActive()
    {
        return $this->adjustment->isActive();
    }

    public final function isAppliedToSubtotal()
    {
        return $this->adjustment->isAppliedToSubtotal();
    }
}

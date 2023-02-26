<?php

namespace Antidote\LaravelCart\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait ConfiguresAdjustment
{
    public function initializeConfiguresAdjustment()
    {
        $this->fillable[] = 'name';
        //$this->fillable[] = 'is_in_subtotal';
        $this->fillable[] = 'class';
        $this->fillable[] = 'parameters';
    }

    public function getCasts() : array
    {
        return array_merge(parent::getCasts(), [
            'parameters' => 'array'
        ]);
    }

    public function order_adjustment()
    {
        return $this->morphOne(config('laravel-cart.classes.order_adjustment'));
    }

    public function calculatedAmount() : Attribute
    {
        return Attribute::make(
            get: fn() => (new $this->class)->calculatedAmount($this->parameters)
        );
    }

    public function isValid() : bool
    {
        return true;
    }

    public function isActive()
    {
        return true;
    }

    public function isAppliedToSubtotal() : bool
    {
        return true;
    }
}

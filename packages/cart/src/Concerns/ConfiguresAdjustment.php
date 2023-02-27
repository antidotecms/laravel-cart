<?php

namespace Antidote\LaravelCart\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

trait ConfiguresAdjustment
{
    public function initializeConfiguresAdjustment()
    {
        $this->fillable[] = 'name';
        $this->fillable[] = 'class';
        $this->fillable[] = 'parameters';
        $this->fillable[] = 'apply_to_subtotal';
        $this->fillable[] = 'is_active';
    }

    public function getCasts() : array
    {
        return array_merge(parent::getCasts(), [
            'parameters' => 'array'
        ]);
    }

    //@todo is this used/needed?
    public function order_adjustment()
    {
        return $this->morphOne(config('laravel-cart.classes.order_adjustment'));
    }


    protected function calculatedAmount() : Attribute
    {
        return Attribute::make(
        //get: fn($value) => (new $this->class($this))->calculatedAmount($this->parameters, $value ?? 0)
            get: fn($value) => $this->getMethodOnAdjustmentIfDefined('calculatedAmount', $value)
        );
    }

    protected function isValid() : Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->getMethodOnAdjustmentIfDefined('isValid', $value)
        );
    }

    protected function isActive() : Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->getMethodOnAdjustmentIfDefined('isActive', $value)
        );
    }

    protected function applyToSubtotal() : Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->getMethodOnAdjustmentIfDefined('applyToSubtotal', $value)
        );
    }

    private function getMethodOnAdjustmentIfDefined($attribute, $value)
    {
        if(method_exists($this->class, $attribute)) {
            $attribute = Str::of($attribute)->studly()->lcfirst();
            return (new $this->class)->{$attribute->value}($this->parameters);
        } else {
            return $value;
        }
    }
}

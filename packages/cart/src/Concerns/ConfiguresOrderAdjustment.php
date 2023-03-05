<?php

namespace Antidote\LaravelCart\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

trait ConfiguresOrderAdjustment
{
    public function getTable()
    {
        return 'order_adjustments';
    }

    public function initializeConfiguresOrderAdjustment() : void
    {
        $this->fillable[] = 'name';
        $this->fillable[] = 'order_id';
        $this->fillable[] = 'amount';
        $this->fillable[] = 'original_parameters';
        $this->fillable[] = 'class';
        $this->fillable[] = 'apply_to_subtotal';
    }

    public function getCasts() : array
    {
        return array_merge(parent::getCasts(), [
            'original_parameters' => 'array'
        ]);
    }

    //@todo refactor all the below into a trait to be shared between OrderAdjustment and Adjustment
    public function isValid(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->getMethodOnAdjustmentIfDefined('isValid', $value)
        );
    }

    public function isActive(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->getMethodOnAdjustmentIfDefined('isActive', $value)
        );
    }

    public function applyToSubtotal(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->getMethodOnAdjustmentIfDefined('applyToSubtotal', $value)
        );
    }

    private function getMethodOnAdjustmentIfDefined($attribute, $value)
    {
        if(method_exists($this->class, $attribute)) {
            $attribute = Str::of($attribute)->studly()->lcfirst();
            return (new $this->class)->{$attribute->value}($this->original_parameters);
        } else {
            return $value;
        }
    }
}

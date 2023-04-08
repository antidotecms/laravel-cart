<?php

namespace Antidote\LaravelCart\Concerns;

/**
 * @property string $name
 * @property bool $apply_to_subtotal
 * @property string $class
 * @property array $original_parameters
 * @property int $amount
 */
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

//    public function applyToSubtotal(): Attribute
//    {
//        return Attribute::make(
//            get: fn($value) => $this->getMethodOnAdjustmentIfDefined('applyToSubtotal', $value)
//        );
//    }
//
//    private function getMethodOnAdjustmentIfDefined($attribute, $value)
//    {
//        if(method_exists($this->class, $attribute)) {
//            $attribute = Str::of($attribute)->studly()->lcfirst()->value();
//            return (new $this->class)->{$attribute}($this->original_parameters);
//        } else {
//            return $value;
//        }
//    }
}

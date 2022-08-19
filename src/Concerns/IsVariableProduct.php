<?php

namespace Antidote\LaravelCart\Concerns;
/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait IsVariableProduct
{
    /**
     * @throws Exception
     */
    public function getName(array $specification): string
    {
        if(isset($this->name)) return $this->name;

        throw new Exception('Attribute `name` not on model. Please override `isCartItem::getName()`');
    }

    /**
     * @throws Exception
     */
    public function getPrice(array $specification): int
    {
        if(isset($this->price)) return $this->price;

        throw new Exception('Attribute `price` not on model. Please override `isCartItem::getPrice()`');
    }
}

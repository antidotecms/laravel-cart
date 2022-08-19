<?php

namespace Antidote\LaravelCart\Concerns;
use Exception;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait IsProduct
{
    /**
     * @throws Exception
     */
    public function getName(): string
    {
        if(isset($this->name)) return $this->name;

        throw new Exception('Attribute `name` not on model. Please override `isCartItem::getName()`');
    }

    /**
     * @throws Exception
     */
    public function getDescription(): string
    {
        if(isset($this->description)) return $this->description;

        throw new Exception('Attribute `description` not on model. Please override `isCartItem::getDescription()`');
    }

    /**
     * @throws Exception
     */
    public function getPrice(): int
    {
        if(isset($this->price)) return $this->price;

        throw new Exception('Attribute `price` not on model. Please override `isCartItem::getPrice()`');
    }
}

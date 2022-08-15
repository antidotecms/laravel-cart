<?php

namespace Antidote\LaravelCart\Concerns;

use Antidote\LaravelCart\Models\Cart;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasCart
{
    /**
     * @return MorphOne
     */
    public function cart(): MorphOne
    {
        return $this
            ->morphOne(Cart::class, 'shopper')
            ->withDefault(function($cart, $customer)
            {
                $cart->customer()->save($customer);
                $cart->save();
            });
    }
}

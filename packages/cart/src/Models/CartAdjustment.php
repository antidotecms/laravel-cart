<?php

namespace Antidote\LaravelCart\Models;

use Antidote\LaravelCart\Facades\Cart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

/**
 * @property array $parameters
 */
class CartAdjustment extends Model
{
    protected $fillable = [
        'name',
        'class',
        'parameters',
        'active'
    ];

    protected $casts = [
        'parameters' => 'array'
    ];

    public function isActive() : bool
    {
        return $this->active;
    }

    public function isValid() : bool
    {
        $adjustment = App::make($this->class, ['adjustment' => $this]);
        return $adjustment->isValid();
    }

    public function amount()
    {
        $adjustment = App::make($this->class, ['adjustment' => $this]);
        return $adjustment->amount(Cart::getSubtotal());
    }
}

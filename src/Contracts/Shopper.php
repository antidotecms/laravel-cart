<?php

namespace Antidote\LaravelCart\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface Shopper
{
    public function cart() : MorphOne;
}

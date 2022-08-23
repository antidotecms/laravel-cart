<?php

namespace Antidote\LaravelCart\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property MorphTo $productDataType
 */
trait IsProduct
{
    public function getName(...$args) : string
    {
        return $this->productDataType->getName(...$args);
    }

    public function getDescription(...$args) : string
    {
        return $this->productDataType->getDescription(...$args);
    }

    public function getPrice(...$args) : int
    {
        return $this->productDataType->getPrice(...$args);
    }

    public function productDataType() : MorphTo
    {
        return $this->morphTo();
    }
}

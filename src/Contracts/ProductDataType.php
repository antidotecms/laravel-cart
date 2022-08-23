<?php

namespace Antidote\LaravelCart\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface ProductDataType
{
    public function getName(?array $product_data = null): string;

    public function getDescription(?array $product_data = null): string;

    public function getPrice(?array $product_data = null): int;

    public function product() : MorphOne;
}

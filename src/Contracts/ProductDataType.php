<?php

namespace Antidote\LaravelCart\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphTo;

interface ProductDataType
{
    public function getName(...$args): string;

    public function getDescription(...$args): string;

    public function getPrice(...$args): int;

    public function type() : MorphTo;
}

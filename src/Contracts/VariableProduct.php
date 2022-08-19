<?php

namespace Antidote\LaravelCart\Contracts;

interface VariableProduct
{
    public function getName(?array $specification): string;

    public function getPrice(?array $specification): int;
}

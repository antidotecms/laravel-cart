<?php

namespace Antidote\LaravelCart\Contracts;

interface VariableProduct
{
    public function getName(): string;

    public function getPrice(array $specification): int;
}

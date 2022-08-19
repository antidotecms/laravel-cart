<?php

namespace Antidote\LaravelCart\Contracts;

Interface Product
{
    public function getName(): string;

    public function getDescription(): string;

    public function getPrice(): int;
}

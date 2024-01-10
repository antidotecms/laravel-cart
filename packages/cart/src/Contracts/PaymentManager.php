<?php

namespace Antidote\LaravelCart\Contracts;

use Antidote\LaravelCart\Models\Order;

abstract class PaymentManager
{
    abstract public function getCheckoutComponent(): string;
    abstract public function updateStatus(Order $order): void;

    abstract public function isCompleted(Order $order): bool;
}

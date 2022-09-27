<?php

namespace Antidote\LaravelCartStripe\Domain\Controllers;

class StripeController
{
    public function getClientSecret(int $order_id)
    {
        $order_class = getClassNameFor('order');
        $order = $order_class::where('id', $order_id)->first();
    }
}

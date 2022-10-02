<?php

namespace Antidote\LaravelCartStripe\Domain\Controllers;

class StripeController
{
    public function confirm()
    {
        //confirm that the order has not already been paid for

        //confirm that all products are active and valid

        //confirm order total to be submitted to stripe is the same as that of the order total
    }

    public function webhook()
    {

    }
}

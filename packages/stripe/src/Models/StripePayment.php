<?php

namespace Antidote\LaravelCartStripe\Models;

use Antidote\LaravelCart\Contracts\Payment;
use Antidote\LaravelCartStripe\Concerns\ConfiguresStripePayment;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;
use Illuminate\Database\Eloquent\Casts\Attribute;

class StripePayment extends Payment
{
    use ConfiguresStripePayment;

    public function initialize(): void
    {
        PaymentIntent::create($this->order);
    }

    public function clientSecret() : Attribute
    {
        return Attribute::make(
            get: function($value) {

                if(!$value) {
                    $value = PaymentIntent::getClientSecret($this->order);
                    $this->client_secret = $value;
                    $this->save();
                }

                return $value;
            }
        );
    }
}

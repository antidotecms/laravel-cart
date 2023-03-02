<?php

namespace Antidote\LaravelCartStripe\Concerns;

use Antidote\LaravelCartStripe\Domain\PaymentIntent;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait ConfiguresStripeOrder
{
    public function initializeConfiguresStripeOrder() : void
    {
        $this->fillable[] = 'payment_intent_id';
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

    public function setClientSecretAttribute($value)
    {
        return $this->setData('client_secret', $value);
    }
}

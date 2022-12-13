<?php

namespace Antidote\LaravelCartStripe\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class AllowStripeWebhooksDuringMaintenance extends Middleware
{
    /**
     * @return array
     */
    public function getExcludedPaths(): array
    {
        return [
            config('laravel-cart.urls.stripe.webhook_handler')
        ];
    }
}

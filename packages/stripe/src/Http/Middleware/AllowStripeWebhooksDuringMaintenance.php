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
            app()->get('filament')->getPlugin('laravel-cart')->getUrl('stripe.webhookHandler')
        ];
    }
}

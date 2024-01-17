<?php

namespace Antidote\LaravelCartStripe\Http\Middleware;

use Antidote\LaravelCartFilament\CartPanelPlugin;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class AllowStripeWebhooksDuringMaintenance extends Middleware
{
    /**
     * @return array
     */
    public function getExcludedPaths(): array
    {
        return [
            CartPanelPlugin::get('stripe.webhookHandler')
        ];
    }
}

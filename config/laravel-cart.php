<?php

return [
    'classes' => [
        'product' => \Antidote\LaravelCart\Models\Product::class,
        'customer' => \Antidote\LaravelCart\Models\Customer::class,
        'order' => \Antidote\LaravelCart\Models\Order::class,
        'order_item' => \Antidote\LaravelCart\Models\OrderItem::class,
        'order_adjustment' => \Antidote\LaravelCart\Contracts\OrderAdjustment::class,
        'adjustment' => \Antidote\LaravelCart\Models\Adjustment::class,
        'payment' => \Antidote\LaravelCart\Contracts\Payment::class,
        'order_log_item' => \Antidote\LaravelCart\Contracts\OrderLogItem::class
    ],
    'filament' => [
        'order' => \Antidote\LaravelCartFilament\Resources\OrderResource::class,
        'customer' => \Antidote\LaravelCartFilament\Resources\CustomerResource::class,
        'adjustment' => \Antidote\LaravelCartFilament\Resources\AdjustmentResource::class
    ],
    'stripe' => [
        'api_key' => env('STRIPE_API_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        /**
         * if log is set to true, events will be written to the standard log file
         * if false, no logging occurs
         * if set as a string, the relevant logging channel will be used
         */
        'log' => false
    ],
    'urls' => [
        'order_complete' => '/checkout/complete',
        'checkout_confirm' => '/checkout/confirm',
        'stripe' => [
            'webhook_handler' => '/checkout/stripe'
        ]
    ],
    'views' => [
        'order_complete' => 'shop.checkout.order_complete'
    ],
    'tax_rate' => '0.2',
    'emails' => [
        'order_complete' => null
    ],
    'adjustments' => []
];

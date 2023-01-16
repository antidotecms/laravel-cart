<?php

return [
    'classes' => [
        'product' => \App\Models\Product::class,
        'customer' => \App\Models\Customer::class,
        'order' => \App\Models\Order::class,
        'order_item' => \App\Models\OrderItem::class,
        'order_adjustment' => \App\Models\OrderAdjustment::class,
        'payment' => \Antidote\LaravelCartStripe\Models\StripePayment::class,
        'order_log_item' => \App\Models\OrderLogItem::class
    ],
    'filament' => [
        'order' => \Antidote\LaravelCartFilament\Resources\OrderResource::class,
        'customer' => \Antidote\LaravelCartFilament\Resources\CustomerResource::class
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
    ]
];

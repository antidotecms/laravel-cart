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
    'stripe' => [
        'api_key' => env('STRIPE_API_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET')
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
    ]
];

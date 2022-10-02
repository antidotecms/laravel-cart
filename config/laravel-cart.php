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
        'secret_key' => env('STRIPE_SECRET_KEY')
    ]
];

<?php

return [
    'classes' => [
        'product' => \App\Models\Product::class,
        'customer' => \App\Models\Customer::class,
        'order' => \App\Models\Order::class,
        'order_item' => \App\Models\OrderItem::class,
        'order_adjustment' => \App\Models\OrderAdjustment::class,
        'payment_method' => \Antidote\LaravelCartStripe\Models\StripePayment::class,
        'order_log_item' => \App\Models\OrderLogItem::class
    ]
];

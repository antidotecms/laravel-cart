<?php

return [
    'classes' => [
        'product' => env('does_not_exist', fn() => throw new \Exception('Configure models in cart plugin')), //\Antidote\LaravelCart\Models\Product::class,
        'customer' => env('does_not_exist', fn() => throw new \Exception('Configure models in cart plugin')), //\Antidote\LaravelCart\Models\Customer::class,
        'order' => env('does_not_exist', fn() => throw new \Exception('Configure models in cart plugin')), //\Antidote\LaravelCart\Models\Order::class,
        'order_item' => env('does_not_exist', fn() => throw new \Exception('Configure models in cart plugin')), //\Antidote\LaravelCart\Models\OrderItem::class,
        'order_adjustment' => env('does_not_exist', fn() => throw new \Exception('Configure models in cart plugin')), //\Antidote\LaravelCart\Models\OrderAdjustment::class,
        'adjustment' => env('does_not_exist', fn() => throw new \Exception('Configure models in cart plugin')), //\Antidote\LaravelCart\Models\Adjustment::class,
        'order_log_item' => env('does_not_exist', fn() => throw new \Exception('Configure models in cart plugin')), //\Antidote\LaravelCart\Models\OrderLogItem::class
    ],
//    'filament' => [
//        'order' => \Antidote\LaravelCartFilament\Resources\OrderResource::class,
//        'customer' => \Antidote\LaravelCartFilament\Resources\CustomerResource::class,
//        'adjustment' => \Antidote\LaravelCartFilament\Resources\AdjustmentResource::class
//    ],
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
        'order_complete' => env('does_not_exist', fn() => throw new \Exception('Configure urls in cart plugin')),
        'checkout_confirm' => env('does_not_exist', fn() => throw new \Exception('Configure urls in cart plugin')),
        'stripe' => [
            'webhook_handler' => env('does_not_exist', fn() => throw new \Exception('Configure urls in cart plugin'))
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

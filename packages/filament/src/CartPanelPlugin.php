<?php

namespace Antidote\LaravelCartFilament;

use Antidote\LaravelCart\Models\Address;
use Antidote\LaravelCart\Models\Adjustment;
use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Models\OrderAdjustment;
use Antidote\LaravelCart\Models\OrderItem;
use Antidote\LaravelCart\Models\OrderLogItem;
use Antidote\LaravelCart\Models\Payment;
use Antidote\LaravelCart\Models\Product;
use Antidote\LaravelCart\Models\Products\SimpleProductType;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource;
use Antidote\LaravelCartFilament\Resources\CustomerResource;
use Antidote\LaravelCartFilament\Resources\OrderResource;
use Antidote\LaravelCartFilament\Resources\ProductResource;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Arr;

class CartPanelPlugin implements Plugin
{
    private array $config = [
        'models' => [
            'product' => Product::class,
            'customer' => Customer::class,
            'order' => Order::class,
            'order_item' => OrderItem::class,
            'order_adjustment' => OrderAdjustment::class,
            'adjustment' => Adjustment::class,
            'order_log_item' => OrderLogItem::class,
            'address' => Address::class,
            'payment_method' => Payment::class
        ],
        'tax_rate' => 0.2,
        'resources' => [
            'order' => OrderResource::class,
            'customer' => CustomerResource::class,
            'adjustment' => AdjustmentResource::class,
            'product' => ProductResource::class
        ],
        'urls' => [
            'dashboard' => '/dashboard',
            'login' => '/login',
            'cart' => '/cart',
            'orderComplete' => '/order-complete',
            'checkout' => '/checkout',
            'checkoutConfirm' => '/checkout-confirm',
            'postCheckout' => '/post-checkout',
            'customer' => '/customer',
            'registration' => '/registration'
        ],
        'views' => [
            'orderComplete' => 'order-complete',
            'cart' => 'cart',
            'registration' => 'laravel-cart::registration',
            'emailVerification' => 'laravel-cart::email-verification'
        ],
        'productTypes' => [
            SimpleProductType::class => 'Simple'
        ],
        'paymentMethods' => [
        ],
        'stripe' => [
            'webhookHandler' => '/checkout/stripe',
            'api_key' => 'api_key',
            'secret_key' => 'secret_key',
            'logging' => true,
            'webhook_secret' => 'webhook_secret'
        ]
    ];

    /** @codeCoverageIgnore */
    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(string $key, mixed $default = null) : mixed
    {
        //$config = app('filament')->getPlugin('laravel-cart')->getConfig();
        $config = static::make()->getConfig();
        return Arr::get($config, $key, $default);
    }

    public static function set(?string $key, mixed $value)
    {
        //$config = &app()->get('filament')->getPlugin('laravel-cart')->getConfig();
        $config = &static::make()->getConfig();
        Arr::set($config, $key, $value);
    }

    protected function &getConfig(): array
    {
        return $this->config;
    }

    public function config(array $config)
    {
        static::set(null, array_replace_recursive(static::getConfig(), $config));
        return $this;
    }

    public function getId(): string
    {
        return 'laravel-cart';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources($this->resources());
    }

    /** @codeCoverageIgnore  */
    public function boot(Panel $panel): void
    {
    }

    private function resources(): array
    {
        return Arr::get($this->config, 'resources');
    }
}

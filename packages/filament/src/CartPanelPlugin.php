<?php

namespace Antidote\LaravelCartFilament;

use Antidote\LaravelCart\Models\Adjustment;
use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Models\OrderAdjustment;
use Antidote\LaravelCart\Models\OrderItem;
use Antidote\LaravelCart\Models\OrderLogItem;
use Antidote\LaravelCart\Models\Product;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource;
use Antidote\LaravelCartFilament\Resources\CustomerResource;
use Antidote\LaravelCartFilament\Resources\OrderResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class CartPanelPlugin implements Plugin
{
    /** Resources */
    private ?string $orderResource = null;
    private ?string $customerResource = null;
    private ?string $adjustmentResource = null;

    /** Urls */
    private array $urls = [
    ];
    private string $orderCompleteUrl = 'order-complete';
    private string $checkoutConfirmUrl = 'checkout-confirm';

    private string $stripeWebhookHandler = 'checkout/stripe';

    /** $models */
    private array $models = [
        'product' => Product::class,
        'customer' => Customer::class,
        'order' => Order::class,
        'order_item' => OrderItem::class,
        'order_adjustment' => OrderAdjustment::class,
        'adjustment' => Adjustment::class,
        'order_log_item' => OrderLogItem::class
    ];

    /** @codeCoverageIgnore */
    public static function make(): static
    {
        return app(static::class);
    }

    public function orderResource(string $orderResource)
    {
        $this->orderResource = $orderResource;
        return $this;
    }

    public function getOrderResource(): string
    {
        return $this->orderResource;
    }

    public function customerResource(string $customerResource)
    {
        $this->customerResource = $customerResource;
        return $this;
    }

    public function getCustomerResource(): string
    {
        return $this->customerResource;
    }

    public function adjustmentResource(string $adjustmentResource)
    {
        $this->adjustmentResource = $adjustmentResource;
        return $this;
    }

    public function getAdjustmentResource(): string
    {
        return $this->adjustmentResource;
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
        return [
            'order' => $this->orderResource ?? OrderResource::class,
            'customer' => $this->customerResource ?? CustomerResource::class,
            'adjustment' => $this->adjustmentResource ?? AdjustmentResource::class
        ];
    }

    public function orderCompleteUrl(string $orderCompleteUrl)
    {
        $this->orderCompleteUrl = $orderCompleteUrl;
        return $this;
    }

    public function getOrderCompleteUrl(): string
    {
        return $this->orderCompleteUrl;
    }

    public function checkoutConfirmUrl(string $checkoutConfirmUrl)
    {
        $this->checkoutConfirmUrl = $checkoutConfirmUrl;
        return $this;
    }

    public function getCheckoutConfirmUrl(): string
    {
        return $this->checkoutConfirmUrl;
    }

    public function urls(array $urls)
    {
        $this->urls = array_merge(
            [
                'checkoutConfirm' => $this->checkoutConfirmUrl,
                'orderComplete' => $this->orderCompleteUrl,
                'stripe.webbokHandler' => $this->stripeWebhookHandler
            ],
            $urls
        );

        return $this;
    }

    public function getUrl(string $name)
    {
        if(!array_key_exists($name, $this->urls)) {
            throw new \Exception('Url key does not exist');
        }

        return $this->urls[$name];
    }

    public function models(array $models)
    {
        $this->models = array_merge(
            $this->models,
            $models
        );

        return $this;
    }

    public function getModel(string $name)
    {
        if(!array_key_exists($name, $this->models)) {
            throw new \Exception('Model key does not exist');
        }

        return $this->models[$name];
    }
}

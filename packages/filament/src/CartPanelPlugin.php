<?php

namespace Antidote\LaravelCartFilament;

use Antidote\LaravelCartFilament\Resources\AdjustmentResource;
use Antidote\LaravelCartFilament\Resources\CustomerResource;
use Antidote\LaravelCartFilament\Resources\OrderResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class CartPanelPlugin implements Plugin
{
    private ?string $orderResource = null;
    private ?string $customerResource = null;
    private ?string $adjustmentResource = null;

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

    public function customerResource(string $customerResource)
    {
        $this->customerResource = $customerResource;
        return $this;
    }

    public function adjustmentResource(string $adjustmentResource)
    {
        $this->adjustmentResource = $adjustmentResource;
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
        return [
            'order' => $this->orderResource ?? OrderResource::class,
            'customer' => $this->customerResource ?? CustomerResource::class,
            'adjustment' => $this->adjustmentResource ?? AdjustmentResource::class
        ];
    }
}

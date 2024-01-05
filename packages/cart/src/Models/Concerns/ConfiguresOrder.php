<?php

namespace Antidote\LaravelCart\Models\Concerns;

use Antidote\LaravelCart\Concerns\Illuminate;
use Antidote\LaravelCart\Enums\PaymentMethod;
use Antidote\LaravelCartFilament\CartPanelPlugin;

/** @mixin Illuminate\Database\Eloquent\Model */

trait ConfiguresOrder
{
    public function getTable()
    {
        return 'orders';
    }

    public function initializeConfiguresOrder() : void
    {
        $customer_class = CartPanelPlugin::get('models.customer');
        $this->fillable[] = (new $customer_class)->getForeignKey();
        $this->append('total');
    }

    public function getCasts() : array
    {
        return array_merge(parent::getCasts(), [
            'payment_method' => PaymentMethod::class
        ]);
    }
}

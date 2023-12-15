<?php

namespace Antidote\LaravelCart\Concerns;

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
}

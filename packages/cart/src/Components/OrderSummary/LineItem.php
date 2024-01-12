<?php

namespace Antidote\LaravelCart\Components\OrderSummary;

use Antidote\LaravelCart\Models\OrderItem;
use Antidote\LaravelCart\Models\Product;
use Illuminate\View\Component;
use Illuminate\View\View;
use function Filament\Support\format_money;

class LineItem extends Component
{
    public string $price;
    public string $name;
    public string $description;
    public int $quantity;
    public function __construct(OrderItem $item)
    {
        $this->price = format_money($item->price, 'GBP', 100);
        $this->name = $item->name;
        $this->description = Product::find($item->product_id)->getDescription($item->product_data);
        $this->quantity = $item->quantity;
    }

    public function render(): View
    {
        return view('laravel-cart::components.order-summary.line-item');
    }
}

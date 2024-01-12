<div class='space-y-4'>
    <span class='text-2xl'>Order #{{ $number }}</span>

    <div>
    @foreach($order->items as $item)
        <x-laravel-cart::order-summary.line-item :item='$item'/>
    @endforeach
    </div>

    <x-laravel-cart::order-summary.totals :order='$order'/>
</div>

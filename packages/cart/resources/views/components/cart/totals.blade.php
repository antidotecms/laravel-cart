<div class='flex flex-col justify-self-end w-1/2'>
    <div class='w-full space-y-4'>
        <div class='flex justify-between'>
            <div>Subtotal</div>
            <div>{{ $this->subtotal }}</div>
        </div>
        <div class='flex justify-between'>
            <div>Tax</div>
            <div>{{ $this->tax }}</div>
        </div>
        <div class='flex justify-between'>
            <div>Total</div>
            <div>{{ $this->total }}</div>
        </div>
    </div>
</div>

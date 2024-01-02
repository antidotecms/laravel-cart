<div class='flex justify-between items-center w-full'>
{{--    @dump($this->cartItem)--}}
    <div class='flex items-center space-x-8'>
        <div class='flex flex-col'>
            {{ $this->productName }}
            <span class='text-sm'>{{ $this->productDescription }}</span>
        </div>
        <div>
            {{ $this->lineTotal }}
        </div>
    </div>
    <div class='w-1/2'>
        {{ $this->form }}
    </div>
</div>

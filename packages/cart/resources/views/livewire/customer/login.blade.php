<div>
    <form wire:enter='login' x-on:keyup.enter='$wire.login()'>
    {{ $this->form }}
    </form>
</div>

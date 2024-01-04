<div>
    @error('fail')
        <x-laravel-cart::error :message='$message'/>
    @enderror
    <form wire:submit="login">
    {{ $this->form }}
        <button>Login</button>
    </form>

</div>

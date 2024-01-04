<div>
    <p>Welcome {{ $this->name }}</p>
    <form wire:submit='save'>
        {{ $this->form }}
        <button>Save</button>
    </form>
</div>

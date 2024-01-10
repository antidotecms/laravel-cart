<div>
    <x-filament-actions::group
    :actions="[
        $this->homeAction(),
        $this->logoutAction(),
        $this->loginAction()
    ]"
    icon="heroicon-s-home"
    color='black'
    dropdown-placement="bottom-end"
    dropdown-width="max-w-[6rem]"
    />

    <x-filament-actions::modals />
</div>

<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}

        <div style="margin-top: 1.5rem; display: flex; flex-wrap: wrap; align-items: center; gap: 1rem;">
            <x-filament::button type="submit">
                {{ __('filament-panels::pages/auth/edit-profile.form.actions.save.label') }}
            </x-filament::button>
            
            <x-filament::button color="gray" tag="a" :href="filament()->getUrl()">
                {{ __('filament-panels::pages/auth/edit-profile.actions.cancel.label') }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}

        <div class="mt-6 flex flex-wrap items-center gap-4">
            <x-filament::button type="submit">
                {{ __('filament-panels::pages/auth/edit-profile.form.actions.save.label') }}
            </x-filament::button>
            
            <x-filament::button color="gray" tag="a" :href="filament()->getUrl()">
                {{ __('filament-panels::pages/auth/edit-profile.actions.cancel.label') }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

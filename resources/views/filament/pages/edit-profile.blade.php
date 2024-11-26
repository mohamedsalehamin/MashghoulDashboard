<x-filament-panels::page>
    {{$this->form}}

    <div class="mt-2">
        <x-filament::button wire:click="submit" size="xl">
            @lang('forms.actions.send')
        </x-filament::button>
    </div>

</x-filament-panels::page>

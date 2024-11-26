<x-filament-panels::page>
    <form wire:submit.prevent="submit">
        {{ $this->form }}

   <div class="mt-2">
       <x-filament::button wire:click="submit" size="xl">
           @lang('forms.actions.send')
       </x-filament::button>
   </div>

    </form>
</x-filament-panels::page>

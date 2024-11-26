<div
    x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('agora'))]"
>

    @if((!$sessionRunning && $record->isDoctorReservation() && $record->isOnline() && $record->isRunning()) ||$key=='VnaXGc5WGr8rIDfubolwTATQRDQ11MEx91DeYgJv')
        <x-filament::button
            wire:click="startSession"
            class="mb-2"
        >
            @lang('forms.actions.start_session')

        </x-filament::button>
    @endif
    <div id="agora-react"></div>


</div>


<x-filament-panels::page>
    @php($settings = new \App\DefaultPanel\Settings\GeneralSettings())
    
    <x-filament::section>
        <div class="flex justify-between mb-6">
            <div>
                <h3 class="text-lg font-medium">@lang("menu.contact_us")</h3>
                <ul class="mt-2 space-y-1">
                    <li>@lang('forms.fields.phone') : {{$settings->app_phone}}</li>
                    <li>@lang('forms.fields.app_whatsapp') : {{$settings->app_whatsapp}}</li>
                    <li>@lang('forms.fields.email') : {{$settings->app_email}}</li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-medium">@lang("sections.social_links")</h3>
                <ul class="flex gap-4 mt-2">
                    @foreach($settings->social_links as $social)
                        <li><a href="{{$social['link']}}" target="_blank"><i class="fab fa-{{Str::replace(" ","",$social['icon'])}}"></i></a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </x-filament::section>
    
    <x-filament::section class="mt-6">
        <form wire:submit.prevent="submit">
            {{ $this->form }}
            
            <div class="mt-6">
                <x-filament::button type="submit">
                    {{ __('forms.actions.send') }}
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>
</x-filament-panels::page>

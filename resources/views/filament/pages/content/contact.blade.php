<x-filament-panels::page>
    @php($settings = new \App\DefaultPanel\Settings\GeneralSettings())
    <div class="flex justify-between">
        <div>
            <h3>@lang("menu.contact_us")</h3>
            <ul>
                <li>@lang('forms.fields.phone') : {{$settings->app_phone}}</li>
                <li>@lang('forms.fields.app_whatsapp') : {{$settings->app_whatsapp}}</li>
                <li>@lang('forms.fields.email') : {{$settings->app_email}}</li>
            </ul>
        </div>

        <div>
            <h3>@lang("sections.social_links")</h3>
            <ul class="flex gap-4">
                @foreach($settings->social_links as $social)
                    <li> <a href="{{$social['link']}}" target="_blank"><i class="fab fa-{{Str::replace(" ","",$social['icon'])}}"></i></a> </li>
                @endforeach

            </ul>
        </div>
    </div>
    <x-filament-panels::form wire:submit="submit">
        {{ $this->form }}
        
        <x-filament-panels::form.actions
            :actions="$this->getFormActions()"
        />
    </x-filament-panels::form>

</x-filament-panels::page>

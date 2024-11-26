<button
    wire:click="$dispatch('openModal',{component:'change-city-pop-up'})"
    class="header-address header-link"
    id="changeLocationIcon"
>
    <i class="fa-regular fa-location-dot"></i>
    <span class="text">{{$current_city->name??__('forms.fields.location')}}</span>
</button>


<input type="tel" data-has-phone
       wire:model="phone"
       class="input"
       value="{{$phone}}"
       autocomplete="off"
       id="phone"
/>
<input type="hidden" id="country_code" wire:model="country_code" value="{{$country_code}}"/>

@push("css")
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/25.2.1/build/css/intlTelInput.min.css" integrity="sha512-X3pJz9m4oT4uHCYS6UjxVdWk1yxSJJIJOJMIkf7TjPpb1BzugjiFyHu7WsXQvMMMZTnGUA9Q/GyxxCWNDZpdHA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush
@push('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/25.2.1/build/js/intlTelInput.min.js" integrity="sha512-IkaM8IicdlJR0eLhPoAHBeDXxQ8QTjVfo7O9hwowr8gTmxZOlV0Z51HFYIDmftcLmdejUlGam6uYVU3k7xP/4A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>

        const telInputs = $("#phone")[0];
        const iti = intlTelInput(telInputs, {
            utilsScript: "utils.js",
            autoPlaceholder: "aggressive",
            separateDialCode: true,
            initialCountry: 'sa',
            onlyCountries: ["sa", 'eg',],

            nationalMode: true
        });
        telInputs.addEventListener("change", function () {
            @this.
            set("country_code", iti.getSelectedCountryData().dialCode);

        });

        telInputs.addEventListener("countrychange", function () {
            @this.set('phone', '')
            @this.set("country_code", iti.getSelectedCountryData().dialCode);

        });

    </script>
@endpush

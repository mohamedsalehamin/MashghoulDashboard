<input type="text" id="phone" data-has-phone class="form-control" wire:model="phone"
       value="{{$phone}}"
       autocomplete="off"
       style="direction: ltr"
/>
<input type="hidden" id="country_code" wire:model="country_code" value="{{$country_code}}"/>


@push('js')
    <script>

        const telInputs = $("#phone")[0];
        const iti = intlTelInput(telInputs, {
            utilsScript: "js/utils.js",
            autoPlaceholder: "aggressive",
            separateDialCode: true,
            'initialCountry': '',
            preferredCountries: ["sa", 'eg', "kw", "ae", "bh", "om", "qa"],

            nationalMode: true
        });
        telInputs.addEventListener("keyup", function () {
            @this.
            set("country_code", iti.getSelectedCountryData().dialCode);

        });
        telInputs.addEventListener("countrychange", function () {
            @this.
            set('phone', '')
            @this.set("country_code", iti.getSelectedCountryData().dialCode);

        });

    </script>
@endpush

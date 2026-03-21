<div wire:ignore>
    <input type="tel" data-has-phone
           class="form-control input"
           value="{{ $phone ?? '' }}"
           autocomplete="off"
           id="phone"
    />
    <input type="hidden" id="country_code" value="{{ $country_code ?? '966' }}"/>
</div>

@push("css")
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/25.2.1/build/css/intlTelInput.min.css" integrity="sha512-X3pJz9m4oT4uHCYS6UjxVdWk1yxSJJIJOJMIkf7TjPpb1BzugjiFyHu7WsXQvMMMZTnGUA9Q/GyxxCWNDZpdHA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .iti { display: block !important; width: 100%; }
        .iti__flag-container,
        .iti__tel-input { display: inline-block; }
        [dir="rtl"] .iti,
        [dir="rtl"] .iti .iti__tel-input { direction: ltr; text-align: left; }
        [dir="rtl"] .iti .iti__country-container,
        [dir="rtl"] .iti .iti__flag-container { left: 0; right: auto; }
        [dir="rtl"] .iti input.iti__tel-input, 
        [dir="rtl"] .iti input.iti__tel-input[type=tel], 
        [dir="rtl"] .iti input.iti__tel-input[type=text] {padding-right: 10px !important;padding-left: 87px !important; }
    </style>
@endpush
@push('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/25.2.1/build/js/intlTelInput.min.js" integrity="sha512-IkaM8IicdlJR0eLhPoAHBeDXxQ8QTjVfo7O9hwowr8gTmxZOlV0Z51HFYIDmftcLmdejUlGam6uYVU3k7xP/4A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        (function () {
            const telInput = document.getElementById("phone");
            if (!telInput || telInput.dataset.itiInit) return;
            telInput.dataset.itiInit = "1";

            const iti = intlTelInput(telInput, {
                utilsScript: "utils.js",
                autoPlaceholder: "aggressive",
                separateDialCode: true,
                initialCountry: 'sa',
                onlyCountries: ["sa"],
                nationalMode: true
            });

            function syncToLivewire() {
                @this.set("country_code", iti.getSelectedCountryData().dialCode);
                @this.set("phone", telInput.value.trim());
            }

            telInput.addEventListener("change", syncToLivewire);
            telInput.addEventListener("blur", syncToLivewire);
            telInput.addEventListener("countrychange", function () {
                @this.set("country_code", iti.getSelectedCountryData().dialCode);
                @this.set("phone", telInput.value.trim());
            });
        })();
    </script>
@endpush

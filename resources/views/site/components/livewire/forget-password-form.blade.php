<div class="form-content">
    <div class="form-group" wire:ignore>
        <label class="form-label"> @lang("site.fields.phone") </label>
        <input type="tel" id="phone" class="form-control" placeholder="5xxxxxxxx" wire:model="phone"/>
        <input type="hidden" wire:model="country_code"/>

    </div>
    @error('phone')
    <p class="text-danger">{{$message}}</p>
    @enderror
    <a
        wire:click="handle"
        class="submit-btn main-btn"
        wire:loading.attr="disabled"
    >
        <div wire:loading class="mx-1">
            @include("site.components.loader")
        </div>
        @lang('site.buttons.send')
    </a>
</div>

@push('js')
    <script>

        const telInputs = $("#phone")[0];
        const iti = intlTelInput(telInputs, {
            utilsScript: "js/utils.js",
            autoPlaceholder: "aggressive",
            separateDialCode: true,
            initialCountry: "sa",
            preferredCountries: ["sa", 'eg', "kw", "ae", "bh", "om", "qa"],

        });
        telInputs.addEventListener("countrychange", function () {
            @this.
            set("country_code", iti.getSelectedCountryData().dialCode);

        });

    </script>
@endpush

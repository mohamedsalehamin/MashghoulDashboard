<div wire:ignore>
    <input type="tel" data-has-phone
           class="form-control input"
           value="{{ $phone ?? '' }}"
           autocomplete="off"
           id="phone"
    />
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

@once
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/25.2.1/build/js/intlTelInput.min.js" integrity="sha512-IkaM8IicdlJR0eLhPoAHBeDXxQ8QTjVfo7O9hwowr8gTmxZOlV0Z51HFYIDmftcLmdejUlGam6uYVU3k7xP/4A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        (function () {
            function findLivewireComponent(el) {
                if (!el || !window.Livewire) return null;
                var root = el.closest('[wire\\:id]');
                if (!root) return null;
                var id = root.getAttribute('wire:id');
                return id ? Livewire.find(id) : null;
            }

            window.syncItiPhoneToLivewire = async function () {
                var telInput = document.getElementById('phone');
                if (!telInput || !telInput._iti) return;
                var comp = findLivewireComponent(telInput);
                if (!comp) return;
                var iti = telInput._iti;
                var data = iti.getSelectedCountryData();
                var dial = data && data.dialCode != null ? String(data.dialCode) : '966';
                var phoneVal = (telInput.value || '').trim();
                if (typeof comp.set !== 'function') return;
                await Promise.resolve(comp.set('country_code', dial));
                await Promise.resolve(comp.set('phone', phoneVal));
            };

            window.initMashghoulPhoneInput = function () {
                var telInput = document.getElementById('phone');
                if (!telInput || typeof intlTelInput === 'undefined') return;
                // Same node after unrelated Livewire morphs — keep existing intl-tel instance.
                if (telInput._iti && telInput.dataset.itiReady === '1') return;

                if (telInput._iti) {
                    try { telInput._iti.destroy(); } catch (e) {}
                    telInput._iti = null;
                }
                delete telInput.dataset.itiReady;

                // v25+ prefers loadUtils(); omitting still shows flag + dial; validation uses libphonenumber on server.
                var iti = intlTelInput(telInput, {
                    autoPlaceholder: 'aggressive',
                    separateDialCode: true,
                    initialCountry: 'sa',
                    onlyCountries: ['sa'],
                    nationalMode: true,
                    // Prevent display normalization that can drop a leading trunk "0" (e.g. 05xxxxxxxx -> 5xxxxxxxx).
                    formatOnDisplay: false
                });
                telInput._iti = iti;
                telInput.dataset.itiReady = '1';

                // Important: do NOT sync on init.
                // intl-tel-input may normalize/format the value on load (e.g. trimming a leading trunk "0"),
                // and pushing that into Livewire immediately would mutate the stored phone even if the user
                // only edits other profile fields.
                var touched = false;

                function sync() {
                    if (!touched) return;
                    window.syncItiPhoneToLivewire();
                }

                function markTouchedAndSync() {
                    touched = true;
                    sync();
                }

                telInput.addEventListener('input', markTouchedAndSync);
                telInput.addEventListener('change', markTouchedAndSync);
                telInput.addEventListener('blur', markTouchedAndSync);
                telInput.addEventListener('countrychange', sync);
            };

            function schedulePhoneInit() {
                queueMicrotask(function () {
                    if (window.initMashghoulPhoneInput) window.initMashghoulPhoneInput();
                });
            }

            function registerMorphHook() {
                if (registerMorphHook._done || typeof Livewire === 'undefined') return;
                registerMorphHook._done = true;
                Livewire.hook('morph.updated', function () {
                    schedulePhoneInit();
                });
            }

            document.addEventListener('livewire:init', registerMorphHook);
            if (window.Livewire) {
                registerMorphHook();
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', schedulePhoneInit);
            } else {
                schedulePhoneInit();
            }
        })();
    </script>
@endpush
@endonce

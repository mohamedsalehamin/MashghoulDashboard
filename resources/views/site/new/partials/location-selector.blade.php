@php
    $assetBase = asset('assets/site');
    $locale = app()->getLocale();
    $userName = auth()->guard('site')->check()
        ? auth()->guard('site')->user()->name
        : __('site.heading.set_location_guest');

    $savedLat = session('user_latitude');
    $savedLng = session('user_longitude');
    $hasSavedLocation = $savedLat !== null && $savedLng !== null;
@endphp

@php
    $inModal = $inModal ?? false;
@endphp

<div class="location-page-wrapper {{ $inModal ? '' : 'min-vh-100' }}">
    <div class="row g-0 {{ $inModal ? '' : 'h-100 min-vh-100' }}">
        <div class="col-lg-5 col-xl-4 {{ $inModal ? '' : 'h-100' }}">
            <div class="location-sidebar {{ $inModal ? '' : 'h-100 min-vh-100' }} d-flex flex-column justify-content-center align-items-center text-center px-3">
                <div class="user-avatar mb-4">
                    @if(auth()->guard('site')->check() && auth()->guard('site')->user()->getFirstMediaUrl('avatar'))
                        <img
                            src="{{ auth()->guard('site')->user()->getFirstMediaUrl('avatar') }}"
                            class="img-fluid rounded-circle"
                            alt="{{ $userName }}"
                            style="width: 120px; height: 120px; object-fit: cover;"
                        >
                    @else
                        <img
                            src="{{ $assetBase }}/images/user.webp"
                            class="img-fluid rounded-circle avatar-img"
                            alt="{{ $userName }}"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';"
                            style="width: 120px; height: 120px; object-fit: cover;"
                        >
                        <span
                            class="rounded-circle bg-light text-dark fw-bold avatar-fallback"
                            style="display: none; width: 120px; height: 120px; font-size: 2.5rem; align-items: center; justify-content: center;"
                        >
                            {{ mb_substr($userName, 0, 1) }}
                        </span>
                    @endif
                </div>

                <h2 class="welcome-text text-white mb-5">{{ __('site.heading.set_location_welcome', ['name' => $userName]) }}</h2>
                <p class="instruction-text text-white mb-4">{{ __('site.heading.set_location_instruction') }}</p>

                <form id="set-location-form" method="POST" action="{{ route('site.set-location.save') }}" class="w-75">
                    @csrf
                    <input
                        type="hidden"
                        name="latitude"
                        id="latitude"
                        value="{{ $hasSavedLocation ? (float) $savedLat : '' }}"
                    >
                    <input
                        type="hidden"
                        name="longitude"
                        id="longitude"
                        value="{{ $hasSavedLocation ? (float) $savedLng : '' }}"
                    >

                    <button
                        type="button"
                        class="btn btn-green px-5 py-3 fz18 w-100 rounded-pill auto-locate-btn"
                        id="auto-locate-btn"
                    >
                        {{ __('site.heading.set_location_auto_btn') }}
                    </button>

                    <button
                        type="submit"
                        class="btn btn-outline-light mt-3 px-5 py-2 rounded-pill d-none"
                        id="save-marker-btn"
                    >
                        {{ __('site.heading.set_location_save_marker') }}
                    </button>
                </form>

                <p id="location-error" class="text-danger small mt-2 d-none"></p>
            </div>
        </div>

        <div class="col-lg-7 col-xl-8 {{ $inModal ? '' : 'h-100' }}">
            <div class="map-container {{ $inModal ? 'w-100' : 'h-100 w-100 min-vh-100' }}" style="{{ $inModal ? 'min-height:60vh;' : '' }}">
                <div
                    id="location-map"
                    class="w-100 {{ $inModal ? '' : 'h-100' }}"
                    style="{{ $inModal ? 'min-height:60vh;' : 'min-height: 100%;' }}"
                ></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @php
        $googleMapsKey = config('filament-google-maps.keys.web_key') ?? config('filament-google-maps.key');
        $defaultLat = 24.7136;
        $defaultLng = 46.6753;
    @endphp
    @if($googleMapsKey)
        <script>
            function initLocationMap() {
                var savedLat = {{ $hasSavedLocation ? (float) $savedLat : 'null' }};
                var savedLng = {{ $hasSavedLocation ? (float) $savedLng : 'null' }};
                var defaultLat = {{ $defaultLat }};
                var defaultLng = {{ $defaultLng }};

                var latInput = document.getElementById('latitude');
                var lngInput = document.getElementById('longitude');
                var saveBtn = document.getElementById('save-marker-btn');
                var autoLocateBtn = document.getElementById('auto-locate-btn');
                var errorEl = document.getElementById('location-error');

                var center;
                var hasPosition = false;
                if (savedLat !== null && savedLng !== null) {
                    center = { lat: savedLat, lng: savedLng };
                    latInput.value = savedLat;
                    lngInput.value = savedLng;
                    hasPosition = true;
                } else {
                    center = { lat: defaultLat, lng: defaultLng };
                }

                var map = new google.maps.Map(document.getElementById('location-map'), {
                    center: center,
                    zoom: 16,
                    mapTypeControl: true,
                    streetViewControl: true,
                    fullscreenControl: true,
                    zoomControl: true
                });

                var marker = new google.maps.Marker({
                    position: center,
                    map: map,
                    draggable: true
                });

                function showSaveButton() {
                    if (saveBtn) saveBtn.classList.remove('d-none');
                }

                function updateInputs() {
                    var pos = marker.getPosition();
                    latInput.value = pos.lat();
                    lngInput.value = pos.lng();
                    showSaveButton();
                }

                if (hasPosition) showSaveButton();

                marker.addListener('dragend', updateInputs);

                if (autoLocateBtn) {
                    autoLocateBtn.addEventListener('click', function() {
                        if (errorEl) errorEl.classList.add('d-none');
                        if (!navigator.geolocation) {
                            if (errorEl) {
                                errorEl.textContent = '{{ __("site.heading.set_location_error") }}';
                                errorEl.classList.remove('d-none');
                            }
                            return;
                        }
                        autoLocateBtn.disabled = true;
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                var pos = { lat: position.coords.latitude, lng: position.coords.longitude };
                                marker.setPosition(pos);
                                map.panTo(pos);
                                map.setZoom(16);
                                updateInputs();
                                autoLocateBtn.disabled = false;
                            },
                            function() {
                                if (errorEl) {
                                    errorEl.textContent = '{{ __("site.heading.set_location_error") }}';
                                    errorEl.classList.remove('d-none');
                                }
                                autoLocateBtn.disabled = false;
                            },
                            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                        );
                    });
                }

                if (hasPosition) updateInputs();
            }
        </script>
        <script>
            (function() {
                var form = document.getElementById('set-location-form');
                var latInput = document.getElementById('latitude');
                var lngInput = document.getElementById('longitude');

                if (!form || !latInput || !lngInput) return;

                form.addEventListener('submit', function(e) {
                    if (!latInput.value || !lngInput.value) {
                        e.preventDefault();
                        return;
                    }
                    localStorage.setItem('user_latitude', latInput.value);
                    localStorage.setItem('user_longitude', lngInput.value);
                    localStorage.setItem('location_set', '1');
                });
            })();
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&callback=initLocationMap" async defer></script>
    @else
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var mapEl = document.getElementById('location-map');
                if (mapEl) mapEl.innerHTML = '<div class="p-4 text-center text-muted">Google Maps API key is not configured.</div>';
            });
        </script>
    @endif
@endpush


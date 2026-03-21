@extends('site.layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $assetBase = asset('assets/site');
    $providerName = $provider->getTranslation('name', $locale);
@endphp
<div class="provider-map-page">
    <div class="container py-3">
        <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('site.categories') }}">{{ __('site.heading.categories') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('site.provider.show', $provider->id) }}">{{ $providerName }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.location_on_map') }}</li>
            </ol>
        </nav>
    </div>
    <div class="map-wrapper" style="height: calc(100vh - 200px); min-height: 400px;">
        <div id="provider-map" class="w-100 h-100"></div>
    </div>
</div>

@push('scripts')
@php
    $googleMapsKey = config('filament-google-maps.keys.web_key') ?? config('filament-google-maps.key');
@endphp
@if($googleMapsKey)
<script>
function initProviderMap() {
    var lat = {{ $lat }};
    var lng = {{ $lng }};
    var providerName = @json($providerName);
    var center = { lat: lat, lng: lng };
    var map = new google.maps.Map(document.getElementById('provider-map'), {
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
        title: providerName
    });
    var infoWindow = new google.maps.InfoWindow({
        content: '<strong>' + providerName + '</strong>'
    });
    marker.addListener('click', function() {
        infoWindow.open(map, marker);
    });
    infoWindow.open(map, marker);
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&callback=initProviderMap" async defer></script>
@else
<script>
document.addEventListener('DOMContentLoaded', function() {
    var el = document.getElementById('provider-map');
    if (el) el.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted p-4">Google Maps API key is not configured.</div>';
});
</script>
@endif
@endpush
@endsection

<?php

use App\ContentModule\Models\Page;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Settings\LandingSettings;

$settings = new GeneralSettings();

$locationSet = session()->has('location_set') && session('location_set') === true;
$shouldShowLocationModal = ! $locationSet && session()->get('show_location_modal') === true;


$locale = app()->getLocale();
$isRtl = $locale === 'ar';
$assetBase = asset('assets/site');
?>
<!doctype html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $metaDescription ?? '' }}">
    @if(!empty($metaKeywords ?? ''))
    <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    @if($isRtl)
        <link rel="stylesheet" href="{{ $assetBase }}/css/bootstrap.rtl.min.css">
    @else
        <link rel="stylesheet" href="{{ $assetBase }}/css/bootstrap.min.css">
    @endif
    <link rel="stylesheet" href="{{ $assetBase }}/css/all.min.css">
    <link rel="stylesheet" href="{{ $assetBase }}/css/animate.css">
    <link rel="stylesheet" href="{{ $assetBase }}/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="{{ $assetBase }}/css/main.css">
    <title>{{ $title ?? __('site.heading.home') }} - {{ config('app.name', 'Mashghol') }}</title>

    @stack('meta')
    @stack("css")
    @livewireStyles
    @if(!empty($settings->code_before_end_head_tag))
    {!! $settings->code_before_end_head_tag !!}
    @endif
</head>
<body>
@if(!empty($settings->code_after_body_tag))
{!! $settings->code_after_body_tag !!}
@endif
@include('site.components.new.header')

<main>
    @yield('content')
</main>

@include('site.components.new.footer')

<div class="floating-icons">
    <div class="up-btn"><i class="fas fa-arrow-up"></i></div>
    @if(isset($settings))
        @php
            $linkItem = collect($settings->social_links ?? [])->firstWhere('icon', 'whatsapp');
            $whatsappNumber = $settings->app_whatsapp ?? (is_array($linkItem) ? ($linkItem['link'] ?? null) : null);
            $whatsappNumber = $whatsappNumber ? preg_replace('/\D/', '', $whatsappNumber) : null;
        @endphp
        @if(!empty($settings->enabled_whatsapp_icon) && $whatsappNumber)
            <a href="https://wa.me/{{ $whatsappNumber }}" class="whatsapp-icon" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i></a>
        @endif
    @endif
</div>
@livewireScripts


@livewire('livewire-ui-modal')
{{-- Favorite toggle success modal --}}
<div class="modal fade custom-bootstrap-modal" id="favorite-modal" tabindex="-1" aria-labelledby="favoriteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header d-flex flex-column align-items-center position-relative pb-0">
                <h3 class="modal-title mb-2" id="favoriteModalLabel">{{ __('site.heading.favorites') }}</h3>
                <p class="modal-subtitle" id="favorite-modal-message"></p>
            </div>
            <div class="modal-footer d-flex flex-row justify-content-center gap-3 pt-0">
                <button type="button" class="btn btn-green modal-confirm px-5" data-bs-dismiss="modal">{{ __('site.buttons.ok') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Location selector modal -->
<div
    class="modal fade"
    id="locationModal"
    tabindex="-1"
    aria-hidden="true"
    @if($shouldShowLocationModal)
        data-bs-backdrop="static"
        data-bs-keyboard="false"
    @endif
    style="overflow:hidden;"
>
    <div class="modal-dialog modal-dialog-centered" style="max-width:80%; width:80%;height:60vh; margin: 1.75rem auto;">
        <div class="modal-content" style="height:60vh; position:relative;">
            @if(! $shouldShowLocationModal)
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                    style="position:absolute; top:12px; @if($isRtl) left:12px; @else right:12px; @endif z-index: 5;"
                ></button>
            @endif
            <div class="modal-body p-0" style="max-height:90vh;">
                @include('site.new.partials.location-selector', ['inModal' => true])
            </div>
        </div>
    </div>
</div>

@if($shouldShowLocationModal)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var el = document.getElementById('locationModal');
                if (!el) return;
                var savedLat = localStorage.getItem('user_latitude');
                var savedLng = localStorage.getItem('user_longitude');
                var hasLocalLocation = !!savedLat && !!savedLng;

                // If localStorage already has a location, sync it to session first.
                if (hasLocalLocation) {
                    var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (csrf) {
                        var form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('site.set-location.save') }}';
                        form.style.display = 'none';

                        var tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = '_token';
                        tokenInput.value = csrf;
                        form.appendChild(tokenInput);

                        var latInput = document.createElement('input');
                        latInput.type = 'hidden';
                        latInput.name = 'latitude';
                        latInput.value = savedLat;
                        form.appendChild(latInput);

                        var lngInput = document.createElement('input');
                        lngInput.type = 'hidden';
                        lngInput.name = 'longitude';
                        lngInput.value = savedLng;
                        form.appendChild(lngInput);

                        document.body.appendChild(form);
                        form.submit();
                        return;
                    }
                }

                // Force a non-closable modal: static backdrop + disable keyboard.
                var modal = bootstrap.Modal.getOrCreateInstance(el, { backdrop: 'static', keyboard: false });
                modal.show();
            });
        </script>
    @endpush
@endif
<script src="{{ $assetBase }}/js/jquery.min.js"></script>
<script src="{{ $assetBase }}/js/bootstrap.min.js"></script>
<script src="{{ $assetBase }}/js/wow.min.js"></script>
<script src="{{ $assetBase }}/js/swiper-bundle.min.js"></script>
<script src="{{ $assetBase }}/js/main.js"></script>
@stack('scripts')

@stack("scripts")
@if(!empty($settings->code_before_end_body_tag))
{!! $settings->code_before_end_body_tag !!}
@endif
</body>
</html>

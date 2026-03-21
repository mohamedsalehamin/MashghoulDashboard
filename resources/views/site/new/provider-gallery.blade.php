@extends('site.layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $assetBase = asset('assets/site');
@endphp
<!-- Start Breadcrumb -->
<div class="container">
    <nav aria-label="breadcrumb" class="my-4 custom-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('site.categories') }}">{{ __('site.heading.categories') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('site.provider.show', $provider->id) }}">{{ $providerName }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.gallery') }}</li>
        </ol>
    </nav>
</div>
<!-- End Breadcrumb -->
<div class="gallery-section mt-5 mb-5">
    <div class="container">
        @forelse($portfolio ?? [] as $album)
        <div class="gallery-section-wrapper mb-4">
            <h3 class="data-card-title">{{ $album['title'] ?: __('site.heading.gallery') }}</h3>
            <div class="row">
                @foreach($album['items'] ?? [] as $item)
                <div class="col-lg-3 col-6 mb-4">
                    <div class="single-gallery-card p-0 border-0 h-100 bg-transparent shadow-none">
                        @if(($item['type'] ?? '') === 'video')
                        <div class="testimonial-media-wrapper m-0 rounded-4 overflow-hidden shadow-sm h-100"
                            onclick="openMediaModal('video', '{{ $item['url'] }}')">
                            <img src="{{ $item['url'] }}" class="testimonial-media-img" alt="{{ $item['title'] ?? '' }}" onerror="this.src='{{ $assetBase }}/images/about.webp'">
                            <div class="play-overlay-btn">
                                <i class="fa-solid fa-play"></i>
                            </div>
                        </div>
                        @elseif(($item['type'] ?? '') === 'audio')
                        <div class="testimonial-media-wrapper testimonial-media-wrapper--dark m-0 rounded-4 overflow-hidden shadow-sm h-100">
                            <div class="audio-player-wrapper">
                                <button class="btn btn-blue play-audio-btn d-flex align-items-center justify-content-center" onclick="toggleAudio(this)">
                                    <i class="fa-solid fa-play"></i>
                                </button>
                                <img src="{{ $assetBase }}/images/audio-waves.png" class="waveform-img" alt="Waveform">
                                <audio src="{{ $item['url'] }}" onended="resetAudio(this)"></audio>
                            </div>
                        </div>
                        @else
                        <div class="testimonial-media-wrapper m-0 rounded-4 overflow-hidden shadow-sm h-100"
                            onclick="openMediaModal('image', '{{ $item['url'] }}')">
                            <img src="{{ $item['url'] }}" class="testimonial-media-img" alt="{{ $item['title'] ?? '' }}">
                        </div>
                        @endif
                        @if(!empty($item['title']))
                        <div class="card-title">{{ $item['title'] }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <p class="text-muted">{{ __('site.no_data') ?? 'لا توجد عناصر في المعرض' }}</p>
            <a href="{{ route('site.provider.show', $provider->id) }}" class="btn btn-blue">{{ __('site.buttons.back') ?? 'رجوع' }}</a>
        </div>
        @endforelse
    </div>
</div>
<!-- Start Media Modal -->
<div class="modal fade" id="mediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-header border-0 justify-content-end p-0 mb-2">
                <button type="button" class="btn-close btn-close-white modal-invert-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0" id="mediaModalBody"></div>
        </div>
    </div>
</div>
<!-- End Media Modal -->
@endsection

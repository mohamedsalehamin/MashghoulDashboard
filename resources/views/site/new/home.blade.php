@extends('site.layouts.app')

@section('content')
@php
    $assetBase = asset('assets/site');
    $locale = app()->getLocale();
    $bannerImgCollection = $locale === 'ar' ? 'en' : 'ar'; // Banner form stores image_ar in collection 'en', image_en in 'ar'
@endphp

<!-- Hero Section -->
<section class="hero-section">
    @if($heroBanners->isEmpty())
        <img src="{{ $assetBase }}/images/hero-banner_optmized.webp" class="img-fluid" alt>
    @elseif($heroBanners->count() === 1)
        @php $b = $heroBanners->first(); @endphp
        <a href="{{ $b->object_type === 'link' ? ($b->object_id ?? '#') : ($b->object_type === 'category' ? route('site.category.show', $b->object_id) : ($b->object_type === 'provider' ? (Route::has('site.provider.show') ? route('site.provider.show', $b->object_id) : '#') : '#')) }}">
            <img src="{{ $b->getFirstMediaUrl($bannerImgCollection) ?: $assetBase . '/images/hero-banner_optmized.webp' }}" class="img-fluid" alt="{{ $b->getTranslation('name', $locale) }}">
        </a>
    @else
        <div class="hero-swiper swiper">
            <div class="swiper-wrapper">
                @foreach($heroBanners as $b)
                    <div class="swiper-slide">
                        <a href="{{ $b->object_type === 'link' ? ($b->object_id ?? '#') : ($b->object_type === 'category' ? route('site.category.show', $b->object_id) : ($b->object_type === 'provider' ? (Route::has('site.provider.show') ? route('site.provider.show', $b->object_id) : '#') : '#')) }}">
                            <img src="{{ $b->getFirstMediaUrl($bannerImgCollection) ?: $assetBase . '/images/hero-banner_optmized.webp' }}" class="img-fluid" alt="{{ $b->getTranslation('name', $locale) }}">
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    @endif
</section>

<!-- Shop By Category -->
<section class="shop-by-category pt-64">
    <div class="container">
        <div class="section-title text-center">{{ __('site.heading.categories') }}</div>
        <div class="shop-by-category-swiper swiper pt-3 pb-5">
            <div class="swiper-wrapper pb-4">
                @foreach($categories as $category)
                    <div class="swiper-slide">
                        <div class="single-category-card">
                            <a href="{{ route('site.category.show', $category->getSlugUrl()) }}">
                                <div class="cat-img">
                                    @if($category->getFirstMediaUrl('icon'))
                                        <img src="{{ $category->getFirstMediaUrl('icon') }}" class="img-fluid" alt="{{ $category->getTranslation('name', $locale) }}">
                                    @else
                                        <img src="{{ $assetBase }}/images/category-1.webp" class="img-fluid" alt="{{ $category->getTranslation('name', $locale) }}">
                                    @endif
                                </div>
                                <div class="cat-title">{{ $category->getTranslation('name', $locale) }}</div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<!-- Home Banners (placement category) -->
@if($categoryBanners->isNotEmpty())
<section class="home-banners pt-64">
    <div class="container">
        <div class="row">
            @foreach($categoryBanners as $b)
                <div class="col-md-6 mb-4">
                    <a href="{{ $b->object_type === 'link' ? ($b->object_id ?? '#') : ($b->object_type === 'category' ? route('site.category.show', $b->object_id) : ($b->object_type === 'provider' ? (Route::has('site.provider.show') ? route('site.provider.show', $b->object_id) : '#') : '#')) }}" class="banner-wrapper">
                        <img src="{{ $b->getFirstMediaUrl($bannerImgCollection) }}" loading="lazy" class="img-fluid" alt="{{ $b->getTranslation('name', $locale) }}">
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if(!empty($aboutUs[$locale]))
<!-- About Us -->
<section class="about-us-section">
    <div class="container">
        <div class="section-title text-center">{{ $aboutUs[$locale]['title'] ?? '' }}</div>
        <div class="row align-items-md-center">
            <div class="col-lg-6">
                <div class="about-wrapper">
                    <div class="about-content">
                        <p class="description">{{ $aboutUs[$locale]['about'] ?? '' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 offset-lg-1">
                @if(!empty($aboutUs[$locale]['image']))
                    <img src="{{ $aboutUs[$locale]['image'] }}" loading="lazy" alt="about" class="about-img img-fluid">
                @endif
            </div>
        </div>
    </div>
</section>
@endif
<!-- Start Products Section: Nearest to you -->
@if($locationSet ?? false)
    <section class="products-section pt-64">
        <div class="container">
            <div class="section-title text-center">{{ __('site.heading.nearest_to_you') }}</div>
            <div class="nearest-providers-swiper products-swiper swiper swiper-x-padding">
                <div class="swiper-wrapper">
                    @forelse($nearestProviders as $provider)
                        <div class="swiper-slide">
                            <x-site.provider-card :provider="$provider" :show-map-button="true" />
                        </div>
                    @empty
                        <div class="swiper-slide"><div class="single-product-card text-center text-muted py-4">{{ __('site.no_data') ?? 'لا توجد بيانات' }}</div></div>
                    @endforelse
                </div>
                <div class="swiper-pagination"></div>
            </div>
            <div class="text-center">
                <a href="{{ route('site.providers.nearest') }}" class="btn btn-blue">{{ __('site.heading.show_all') }}</a>
            </div>
        </div>
    </section>
@endif
<!-- End Products Section -->

<!-- Start Products Section: Most rated -->
<section class="products-section pt-64">
    <div class="container">
        <div class="section-title text-center">{{ __('site.heading.most_rated') }}</div>
        <div class="most-rated-providers-swiper products-swiper swiper swiper-x-padding">
            <div class="swiper-wrapper">
                @forelse($mostRatedProviders as $provider)
                    <div class="swiper-slide">
                        <x-site.provider-card :provider="$provider" :show-map-button="true" />
                    </div>
                @empty
                    <div class="swiper-slide"><div class="single-product-card text-center text-muted py-4">{{ __('site.no_data') ?? 'لا توجد بيانات' }}</div></div>
                @endforelse
            </div>
            <div class="swiper-pagination"></div>
        </div>
        <div class="text-center">
            <a href="{{ route('site.providers.most-rated') }}" class="btn btn-blue">{{ __('site.heading.show_all') }}</a>
        </div>
    </div>
</section>
<!-- End Products Section -->
@if(!empty($appDownload[$locale]))
<section class="app-wrapper pt-64">
    <div class="container">
        <div class="row align-items-md-center">
            @if($appDownload[$locale]['image'])
                <div class="col-md-5">
                    @php
                        $appDownloadImage = (string) $appDownload[$locale]['image'];
                        $appDownloadImageUrl = str_starts_with($appDownloadImage, 'http://') || str_starts_with($appDownloadImage, 'https://')
                            ? $appDownloadImage
                            : \Illuminate\Support\Facades\Storage::url($appDownloadImage);
                    @endphp
                    <img src="{{ $appDownloadImageUrl }}" loading="lazy" class="img-fluid" alt>
                </div>
            @endif
            <div class="col-md-7">
                <div class="app-content-wrapper text-center">
                    <div class="app-title">{{ $appDownload[$locale]['title'] ?? '' }}</div>
                    <div class="app-description">{{ $appDownload[$locale]['description'] ?? '' }}</div>
                    <div class="app-icon">
                        <!-- <div class="app-qr">
                            <img src="{{ $assetBase }}/images/qr.webp" loading="lazy" class="img-fluid" alt>
                        </div> -->
                        <div class="app-buttons">
                            @if($settings->applications_links['client']['apple_store_link'])
                            <a href="{{ $settings->applications_links['client']['apple_store_link']  }}"><img src="{{ $assetBase }}/images/apple-store.webp" loading="lazy" class="img-fluid" alt></a>
                            @endif
                            @if($settings->applications_links['client']['google_play_link'])
                                <a href="{{ $settings->applications_links['client']['google_play_link']  }}"><img src="{{ $assetBase }}/images/play-store.webp" loading="lazy" class="img-fluid" alt></a>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endif
@if(!empty($faqs))
<!-- Start Faqs Section -->
<section class="faqs-section pt-64">
    <div class="text-center">
        <div class="section-title mb-5">{{ __('site.heading.faqs') }}</div>
    </div>
    <div class="container">
        <div class="row align-items-md-center">
            <div class="col-lg-10 offset-lg-1">
                <div class="faqs-wrapper">
                    @foreach($faqs as $faq)
                        <div class="single-faq-item">
                            <div class="faq-title">{{ $faq->getTranslation('question', $locale) }}</div>
                            <div class="faq-content">{{ $faq->getTranslation('answer', $locale) }}</div>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<!-- End Faqs Section -->
@endif
<!-- Start Testimonials Section -->
@if(!empty($testimonials))
<section class="testimonials-section py-64">
    <div class="container">
        <div class="text-center">
            <div class="section-title">{{ __('site.heading.customer_reviews')}}</div>
        </div>

        <div class="testimonials-swiper swiper">
            <div class="swiper-wrapper">
                @foreach($testimonials as $testimonial)
                @php
                    $tName = $testimonial['name_' . $locale] ?? $testimonial['name_ar'] ?? $testimonial['name_en'] ?? '';
                    $tText = $testimonial['text_' . $locale] ?? $testimonial['text_ar'] ?? $testimonial['text_en'] ?? '';
                @endphp
                <div class="swiper-slide">
                    <div class="single-testimonial-card">
                        <div class="user-info">
                            <div class="d-flex align-items-center gap-2">
                                @if(!empty($testimonial['avatar']))
                                    <img src="{{ asset('storage/' . $testimonial['avatar']) }}" alt="{{ $tName }}" class="img-fluid d-block">
                                @else
                                    <img src="{{ $assetBase }}/images/user.webp" alt="{{ $tName }}" class="img-fluid d-block">
                                @endif
                                <div class="user-rate-holder">
                                    <div class="name">{{ $tName }}</div>
                                    <div class="rate">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa-solid fa-star{{ $i <= ($testimonial['rating'] ?? 5) ? '' : ' text-muted' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <div class="rview-date">
                                <span>{{ \Carbon\Carbon::parse($testimonial['date'])->translatedFormat('M j, Y') }}</span>
                                <i class="fa-solid fa-calendar"></i>
                            </div>
                        </div>

                        @if(($testimonial['type'] ?? 'text') === 'text')
                            <div class="testimonial-media-wrapper p-4 text-center">
                                "{{ $tText }}"
                            </div>
                        @elseif($testimonial['type'] === 'image')
                            <div class="testimonial-media-wrapper" onclick="openMediaModal('image', '{{ asset('storage/' . $testimonial['media']) }}')">
                                <img src="{{ asset('storage/' . $testimonial['media']) }}" class="testimonial-media-img" alt="Review">
                            </div>
                        @elseif($testimonial['type'] === 'video')
                            @php $videoUrl = asset('storage/' . $testimonial['media']); @endphp
                            <div class="testimonial-media-wrapper" onclick="openMediaModal('video', '{{ $videoUrl }}')">
                                <img src="{{ $assetBase }}/images/about.webp" class="testimonial-media-img testimonial-video-thumb" alt="Video" data-video-thumbnail="{{ e($videoUrl) }}">
                                <div class="play-overlay-btn"><i class="fa-solid fa-play"></i></div>
                            </div>
                        @elseif($testimonial['type'] === 'audio')
                            <div class="testimonial-media-wrapper border-top">
                                <div class="audio-player-wrapper">
                                    <button class="btn btn-blue play-audio-btn d-flex align-items-center justify-content-center flex-shrink-0" onclick="toggleAudio(this)">
                                        <i class="fa-solid fa-play"></i>
                                    </button>
                                    <img src="{{ $assetBase }}/images/audio-waves.png" class="waveform-img" alt="Waveform">
                                    <audio src="{{ asset('storage/' . $testimonial['media']) }}" onended="resetAudio(this)"></audio>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>
@endif
<!-- End Testimonials Section -->

<!-- Start Media Modal -->
<div class="modal fade" id="mediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-header border-0 justify-content-end p-0 mb-2">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                    style="filter: invert(1);"></button>
            </div>
            <div class="modal-body text-center p-0" id="mediaModalBody">
            </div>
        </div>
    </div>
</div>
<!-- End Media Modal -->
@push('scripts')
@if($heroBanners->count() > 1)
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Swiper !== 'undefined') {
        new Swiper('.hero-swiper', { loop: true, pagination: { el: '.hero-swiper .swiper-pagination' }, autoplay: { delay: 4000 } });
    }
});
</script>
@endif
<script>
    // Capture a random frame from each video testimonial for the thumbnail
    document.querySelectorAll('.testimonial-video-thumb[data-video-thumbnail]').forEach(function(img) {
        var videoUrl = img.getAttribute('data-video-thumbnail');
        if (!videoUrl) return;
        var video = document.createElement('video');
        video.muted = true;
        video.playsInline = true;
        video.preload = 'metadata';
        video.addEventListener('loadedmetadata', function() {
            var duration = video.duration;
            if (!duration || !isFinite(duration)) {
                video.currentTime = 0.5;
            } else {
                var minT = Math.max(0, duration * 0.1);
                var maxT = Math.max(minT, duration * 0.9);
                video.currentTime = minT + Math.random() * (maxT - minT);
            }
        });
        video.addEventListener('seeked', function() {
            try {
                var canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                if (canvas.width && canvas.height) {
                    var ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0);
                    img.src = canvas.toDataURL('image/jpeg', 0.85);
                }
            } catch (e) {}
            video.remove();
        });
        video.addEventListener('error', function() { video.remove(); });
        video.src = videoUrl;
        video.load();
    });
</script>
@endpush
@endsection

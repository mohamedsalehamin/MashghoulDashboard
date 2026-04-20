@props([
    'provider',
    'showMapButton' => true,
])
@php
    $locale = app()->getLocale();
    $assetBase = asset('assets/site');
    $siteUserId = site()->user()?->id;
    $isFavorited = $siteUserId ? $provider->isFavorited($siteUserId) : false;
@endphp
<div class="single-product-card">
    <div class="product-img">
        <div class="add-to-wishlist {{ $isFavorited ? 'active' : '' }}"
             role="button"
             tabindex="0"
             data-favorite-toggle
             data-url="{{ route('site.provider.favorite.toggle', $provider) }}"
             data-initial-state="{{ $isFavorited ? '1' : '0' }}"
             aria-label="{{ __('site.heading.favorites') }}"><i class="fa-regular fa-heart"></i></div>
        <a href="{{ route('site.provider.show', $provider->id) }}">
            @if($provider->getDisplayImageUrl())
                <img src="{{ $provider->getDisplayImageUrl() }}" class="img-fluid" alt="{{ $provider->getTranslation('name', $locale) }}">
            @else
                <img src="{{ $assetBase }}/images/product-demo_optmized.webp" class="img-fluid" alt="{{ $provider->getTranslation('name', $locale) }}">
            @endif
        </a>
    </div>
    <div class="product-info">
        <div class="product-title"><a href="{{ route('site.provider.show', $provider->id) }}">{{ $provider->getTranslation('name', $locale) }}</a></div>
        @if($provider->rate_avg_rate)
            <div class="rating">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fa-solid fa-star {{ $i <= round($provider->rate_avg_rate) ? '' : 'text-muted' }}"></i>
                @endfor
            </div>
        @else
            <div class="rating">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fa-solid fa-star text-muted"></i>
                @endfor
            </div>
        @endif
        @if($showMapButton && isset($provider->distance))
            <div class="distance">{{ __('site.heading.distance_approx') }} {{ number_format($provider->distance / 1000, 1) }} كم</div>
        @endif
        @if($showMapButton && Route::has('site.provider.map'))
            <a href="{{ route('site.provider.map', $provider->id) }}" class="btn btn-green w-100"><i class="fa-regular fa-map"></i> {{ __('site.buttons.show_on_map') }}</a>
        @endif
        <a href="{{ route('site.provider.show', $provider->id) }}" class="btn btn-green-outline w-100 mt-2">{{ __('site.buttons.book_service') }}</a>
    </div>
</div>

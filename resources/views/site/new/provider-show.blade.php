@extends('site.layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $assetBase = asset('assets/site');
    $providerName = $provider->getTranslation('name', $locale);
    $providerBio = $provider->getTranslation('bio', $locale);
    $avgRate = (float) ($provider->rate_avg_rate ?? $provider->rate()->avg('rate') ?? 0);
    $media = $provider->getMedia('images');
    $firstSeat = $seats->first();
    $reservationFees = 0;
    try {
        $reservationFees = $provider->reservation_fees_include_taxes ?? 0;
    } catch (\Throwable $e) {
        $reservationFees = 0;
    }
    $hasBio = !empty(trim((string) $providerBio));
    $hasRates = count($latestRates ?? []) > 0;
    $hasPortfolio = !empty($portfolio) && collect($portfolio)->contains(fn ($a) => !empty($a['items'] ?? []));
    $hasCoupons = count($availableCoupons ?? []) > 0;
@endphp
@if(session('warning'))
<div class="container mt-3">
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endif
<!-- Start Breadcrumb -->
<div class="container">
    <nav aria-label="breadcrumb" class="my-4 custom-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('site.categories') }}">{{ __('site.heading.categories') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $providerName }}</li>
        </ol>
    </nav>
</div>
<!-- End Breadcrumb -->
<div class="salon-hero">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <div class="salon-hero-slider">
                        <div class="swiper">
                        @if($media->isEmpty())
                            <img src="{{ $assetBase }}/images/about.webp" class="img-fluid salon-hero-img" alt="{{ $providerName }}">
                        @else
                            <div class="swiper salon-hero-swiper">
                                <div class="swiper-wrapper">
                                    @foreach($media->take(5) as $m)
                                        <div class="swiper-slide">
                                            <img src="{{ $m->getUrl() }}" class="img-fluid salon-hero-img" alt="{{ $providerName }}">
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                        @endif
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <h1 class="salon-hero-title">{{ $providerName }}</h1>
                    <div class="salon-stats">
                        <div class="stat-card">
                            <div class="stat-label">{{ __('site.heading.average_reviews') }}</div>
                            <div class="stat-number">{{ $avgRate }}</div>
                            <div class="stat-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= round($avgRate) ? '' : 'text-muted' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">{{ __('site.heading.distance') }}</div>
                            <div class="stat-number">{{ number_format($provider->distance / 1000, 1) }} <span>{{ __('site.heading.km') }}</span></div>
                            <div class="stat-icon">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('site.provider.gallery', $provider->id) }}" class="btn btn-blue mt-4 px-5">{{ __('site.heading.gallery') }}</a>
                </div>

            </div>
        </div>

        @if($hasCoupons)
        @push('css')
        <style>
        .coupon-provider-section .coupon-swiper-outer {
            position: relative;
            width: 100%;
            max-width: 100%;
            margin-bottom: 40px;
        }
        .coupon-provider-section .provider-coupons-swiper {
            position: relative;
            width: 100%;
            max-width: 100%;
            margin-inline: 0;
            padding-inline: 0;
            padding-bottom: 40px;
            overflow: hidden;
        }
        .coupon-provider-section .provider-coupons-swiper .swiper-wrapper {
            max-width: 100%;
            box-sizing: border-box;
        }
        .coupon-provider-section .provider-coupons-swiper .swiper-slide {
            height: auto;
            box-sizing: border-box;
        }
        /* Pagination must live INSIDE .swiper so Swiper 11 updates bullets on drag/touch (external el breaks sync). */
        .coupon-provider-section .provider-coupons-swiper .coupon-provider-pagination.swiper-pagination {
            position: absolute;
            left: 0;
            right: 0;
            width: 100%;
            top: auto;
            bottom: 0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            z-index: 3;
            pointer-events: auto;
        }
        .coupon-provider-section .provider-coupons-swiper .coupon-provider-pagination .swiper-pagination-bullet {
            opacity: 0.45;
            background: var(--bs-secondary-color, #adb5bd);
        }
        .coupon-provider-section .provider-coupons-swiper .coupon-provider-pagination .swiper-pagination-bullet-active {
            opacity: 1;
            background: #002f87;
            transform: scale(1.15);
        }
        </style>
        @endpush
        <div class="coupon-provider-section mt-5 mb-5">
            <div class="container">
                <h3 class="data-card-title mb-4">{{ __('site.heading.provider_coupons') }}</h3>
                <div class="coupon-swiper-outer">
                    <div class="provider-coupons-swiper products-swiper swiper">
                        <div class="swiper-wrapper">
                            @foreach($availableCoupons as $coupon)
                            <div class="swiper-slide">
                                <div class="data-card h-100">
                                    <div class="data-list services-data-list mb-2">
                                        <div class="data-item">
                                            <span class="label"><i class="fa-solid fa-angles-left text-green ms-2"></i>
                                                {{ __('site.heading.discount_value') }}</span>
                                            <span class="value">{{ $coupon['display_value'] }}@if($coupon['discount_type'] === 'fixed') <i class="icon-saudi_riyal"></i>@endif</span>
                                        </div>
                                        <div class="data-item">
                                            <span class="label"><i class="fa-solid fa-angles-left text-green ms-2"></i>
                                                {{ __('site.heading.expiration_date') }}</span>
                                            <span class="value">{{ $coupon['end_date'] }}</span>
                                        </div>
                                        <div class="data-item">
                                            <span class="label"><i class="fa-solid fa-angles-left text-green ms-2"></i>
                                                {{ __('site.heading.coupon_applies_to') }}</span>
                                            <span class="value">{{ $coupon['applies_to'] ?? '—' }}</span>
                                        </div>
                                        <div class="data-item">
                                            <span class="label"><i class="fa-solid fa-angles-left text-green ms-2"></i>
                                                {{ __('site.heading.coupon_min_order') }}</span>
                                            <span class="value">
                                                @if(!empty($coupon['min_order_amount']))
                                                    {{ $coupon['min_order_amount'] }} <i class="icon-saudi_riyal"></i>
                                                    @if(!empty($coupon['min_order_type_label']))
                                                        <span class="text-muted small d-inline-block ms-1">({{ $coupon['min_order_type_label'] }})</span>
                                                    @endif
                                                @else
                                                    {{ __('site.coupon_min_order.no_minimum') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mt-4 border-top pt-3">
                                        <span class="text-danger">{{ __('site.heading.terms_and_conditions') }}</span>
                                        <button type="button" class="btn btn-blue py-2 px-5 fz16 copy-coupon-btn" data-code="{{ $coupon['code'] }}" data-copy-text="{{ __('site.buttons.copy') }}" data-copied-text="{{ __('forms.fields.copied') }}">{{ __('site.buttons.copy') }}</button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div id="coupon-provider-swiper-pagination" class="swiper-pagination coupon-provider-pagination" aria-live="polite"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="salon-services-section">
            <div class="container" id="salon-services-container">
            @if($seats->isNotEmpty())
            <ul class="nav nav-tabs chair-tabs" role="tablist">
                @foreach($seats as $seatIndex => $seat)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $seatIndex === 0 ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#chair-{{ $seat['id'] }}" type="button" role="tab">{{ $seat['title'] ?? __('site.heading.chair') . ' ' . ($seatIndex + 1) }}</button>
                </li>
                @endforeach
            </ul>

            <div class="tab-content">
                @foreach($seats as $seatIndex => $seat)
                <div class="tab-pane {{ $seatIndex === 0 ? 'active' : '' }}" id="chair-{{ $seat['id'] }}" role="tabpanel">
                    <div class="salon-search" data-chair-id="{{ $seat['id'] }}">
                        <input type="text" placeholder="{{ __('site.placeholder.search_service') }}" class="service-search-input" data-chair-id="{{ $seat['id'] }}">
                        <i class="fa-light fa-magnifying-glass search-icon"></i>
                        <i class="fa-solid fa-times search-reset-icon d-none" role="button" tabindex="0" aria-label="{{ __('site.buttons.reset') }}" title="{{ __('site.buttons.reset') }}"></i>
                    </div>

                    <div class="category-filters d-flex align-items-center gap-2 mb-4 overflow-auto pb-1">
                        <button class="btn category-filter-btn active" data-filter="all" data-chair-id="{{ $seat['id'] }}">{{ __('site.heading.all') ?? 'الكل' }}</button>
                        @foreach($seat['service_groups'] ?? [] as $group)
                        <button class="btn category-filter-btn" data-filter="{{ $group['id'] }}" data-chair-id="{{ $seat['id'] }}">{{ $group['title'] ?? '' }}</button>
                        @endforeach
                    </div>

                    <div class="services-list" data-chair-id="{{ $seat['id'] }}">
                        @foreach($seat['services'] ?? [] as $svcIndex => $svc)
                        @php
                            $svcTitle = is_array($svc->title ?? null) ? ($svc->getTranslation('title', $locale) ?? '') : ($svc->title ?? '');
                            $svcDesc = is_array($svc->description ?? null) ? ($svc->getTranslation('description', $locale) ?? '') : ($svc->description ?? '');
                            $svcGroupId = $svc->pivot_service_group_id ?? $svc->pivot?->service_group_id ?? null;
                            $basePrice = $svc->sale_price && $svc->sale_price->getAmount() > 0 ? $svc->sale_price : $svc->price;
                            $priceFormatted = $basePrice->formatByDecimal();
                            $priceAmount = $basePrice->getAmount();
                            $products = $svc->products ?? collect();
                        @endphp
                        <input type="checkbox" name="service_{{ $seat['id'] }}" id="svc_{{ $seat['id'] }}_{{ $svc->id }}" class="service-checkbox d-none">
                        <div class="service-card" data-category="{{ $svcGroupId ?? 'all' }}" data-service-id="{{ $svc->id }}">
                            <div class="d-flex gap-3 mb-3">
                                <div class="flex-shrink-0">
                                    @if($svc->getFirstMediaUrl())
                                    <img src="{{ $svc->getFirstMediaUrl() }}" class="rounded-3 object-fit-cover service-img" alt="{{ $svcTitle }}">
                                    @else
                                    <img src="{{ $assetBase }}/images/about.webp" class="rounded-3 object-fit-cover service-img" alt="{{ $svcTitle }}">
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="service-name-wrap mb-1">
                                        <span class="service-name fw-bold">{{ $svcTitle }}</span>
                                    </div>
                                    <div class="service-duration mb-2">( {{ __('site.duration_minutes') ?? 'مدة التنفيذ' }} {{ $svc->duration ?? 0 }} {{ __('site.minutes') ?? 'دقيقة' }} )</div>
                                    @if($svcDesc)
                                    <p class="service-desc text-muted m-0">{{ $svcDesc }}</p>
                                    @endif
                                </div>
                            </div>

                            @if($products->isNotEmpty())
                            <div class="optional-products mt-3 rounded-4 bg-white">
                                <div class="d-flex align-items-center justify-content-between p-3 optional-products-toggle collapsed"
                                    data-bs-toggle="collapse" data-bs-target="#optProd_{{ $seat['id'] }}_{{ $svc->id }}" aria-expanded="false"
                                    aria-controls="optProd_{{ $seat['id'] }}_{{ $svc->id }}">
                                    <h4 class="optional-products-title m-0 border-0 p-0 fs-6 fw-bold text-dark">{{ __('site.heading.optional_products') ?? 'منتجات اختيارية' }}</h4>
                                    <i class="fa-solid fa-plus text-blue accordion-icon"></i>
                                </div>
                                <div class="collapse" id="optProd_{{ $seat['id'] }}_{{ $svc->id }}">
                                    <div class="products-list px-3 pb-3">
                                        @foreach($products as $prod)
                                        @php
                                            $prodPrice = $prod->sale_price && $prod->sale_price->getAmount() > 0 ? $prod->sale_price : $prod->price;
                                            $prodPriceFormatted = $prodPrice->formatByDecimal();
                                            $prodPriceAmount = $prodPrice->getAmount();
                                        @endphp
                                        <div class="product-item d-flex align-items-center py-3 border-bottom border-light">
                                            <div class="d-flex align-items-center gap-3 w-50">
                                                @if($prod->getFirstMediaUrl())
                                                <img src="{{ $prod->getFirstMediaUrl() }}" class="rounded-3 object-fit-cover product-img" alt="{{ is_array($prod->title ?? null) ? ($prod->title[$locale] ?? '') : ($prod->title ?? '') }}">
                                                @else
                                                <img src="{{ $assetBase }}/images/about.webp" class="rounded-3 object-fit-cover product-img" alt="">
                                                @endif
                                                <div class="product-info">
                                                    <div class="product-name fw-bold text-dark">{{ is_array($prod->title ?? null) ? ($prod->title[$locale] ?? $prod->title['ar'] ?? $prod->title['en'] ?? '') : ($prod->title ?? '') }}</div>
                                                    <div class="product-detail text-muted">{{ $prod->meta_data['detail'] ?? '' }}</div>
                                                </div>
                                            </div>
                                            <div class="product-price fw-bold text-blue w-25 text-center">{{ $prodPriceFormatted }} <i class="icon-saudi_riyal"></i></div>
                                            <div class="d-flex align-items-center justify-content-end gap-3 w-25">
                                                <div class="product-qty-btn add-btn bg-blue border-0 d-flex align-items-center justify-content-center" role="button" data-price="{{ $prodPriceAmount }}" data-product-id="{{ $prod->id }}"><i class="fa-solid fa-plus text-white"></i></div>
                                                <span class="product-qty-display fw-bold fs-6">0</span>
                                                <div class="product-qty-btn remove-btn border-0 d-flex align-items-center justify-content-center bg-secondary" role="button" data-price="{{ $prodPriceAmount }}" data-product-id="{{ $prod->id }}"><i class="fa-solid fa-minus text-white"></i></div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="d-flex align-items-center justify-content-between mt-3 pt-3 border-top border-light">
                                <div class="service-price m-0 px-2" data-base="{{ $priceAmount }}" data-current="{{ $priceAmount }}">{{ $priceFormatted }} <i class="icon-saudi_riyal"></i></div>
                                <label class="btn btn-blue px-4 py-2 rounded-5 d-flex align-items-center gap-2 m-0 select-service-btn" for="svc_{{ $seat['id'] }}_{{ $svc->id }}" data-service-id="{{ $svc->id }}" data-seat-id="{{ $seat['id'] }}">
                                    <i class="fa-regular fa-circle-check fw-normal"></i>
                                    <span class="btn-text">{{ __('site.buttons.select_service') ?? 'تحديد الخدمة' }}</span>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif
            

                <div class="booking-bar d-none" id="provider-booking-bar"
                     data-provider-id="{{ $provider->id }}"
                     data-reservation-fees="{{ $reservationFees }}"
                     data-add-to-cart-url="{{ route('site.provider.cart.add', $provider->id) }}"
                     data-login-url="{{ route('site.login') }}?intended={{ urlencode(route('site.provider.show', $provider->id)) }}"
                     data-is-guest="{{ auth()->guard('site')->guest() ? '1' : '0' }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="booking-price-info">
                            <div class="booking-total" data-booking-total>0 <i class="icon-saudi_riyal"></i></div>
                            <div class="booking-fee" data-booking-fee>{{ __('site.reservation_fees') ?? 'رسوم الحجز' }} {{ \Cknow\Money\Money::parse($reservationFees)->formatByDecimal() }} <i class="icon-saudi_riyal"></i></div>
                        </div>
                        <form method="POST" action="{{ route('site.provider.cart.add', $provider->id) }}" id="provider-add-to-cart-form" class="d-inline">
                            @csrf
                            <input type="hidden" name="seat_id" id="provider-cart-seat-id" value="">
                            <div id="provider-cart-services-inputs"></div>
                            <button type="submit" class="btn btn-green booking-btn" id="provider-booking-btn">{{ __('site.buttons.book_now') ?? 'احجز الان' }}</button>
                        </form>
                    </div>
                </div>

                <ul class="nav nav-tabs desc-tabs" role="tablist">
                    @if($hasBio)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#desc-tab" type="button"
                            role="tab">{{ __('site.heading.description') }}</button>
                    </li>
                    @endif

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#schedule-tab" type="button"
                            role="tab">{{ __('site.heading.working_hours') }}</button>
                    </li>

                    @if($hasRates)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews-tab" type="button"
                                role="tab">{{ __('site.heading.reviews') }}</button>
                        </li>
                    @endif
                </ul>
                <div class="tab-content desc-tab-content">
                    @if($hasBio)
                        <div class="tab-pane active" id="desc-tab" role="tabpanel">
                            <p>{{ $providerBio }}</p>
                        </div>
                    @endif

                    <div class="tab-pane" id="schedule-tab" role="tabpanel">
                        <div class="data-card w-md-50 bg-transparent shadow-none border-0 ps-0">
                            <div class="data-list working-hours ">
                            @forelse($workingDays as $wd)
                                <div class="data-item">
                                    <span class="label"><i class="fa-solid fa-angles-left text-green ms-2"></i> {{ $wd['day'] ?? $wd['day_name'] }}</span>
                                    <span class="value">{{ $wd['from'] ?? '' }} - {{ $wd['to'] ?? '' }}</span>
                                </div>
                            @empty
                                <p class="text-muted">{{ __('site.no_working_hours') }}</p>
                            @endforelse
                            </div>
                        </div>
                    </div>

                    @if($hasRates)
                        <div class="tab-pane" id="reviews-tab" role="tabpanel">
                            @foreach($latestRates as $rate)
                            <div class="rating-display-item data-card mb-4">
                                <div class="rating-header d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                    <div class="rating-label fw-bold text-dark">{{ $rate['name'] }}</div>
                                    <div class="rate-date">{{ $rate['created_at'] }}</div>
                                </div>
                                @if(!empty($rate['place']))
                                <div class="rating-header d-flex justify-content-between align-items-center mb-2">
                                    <div class="rating-label fw-bold text-blue">{{ __('site.place_rating') ?? 'المكان' }}</div>
                                    <div class="star-rating static text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star {{ $i <= ($rate['place']['rate'] ?? 0) ? '' : 'text-muted' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                @if(!empty($rate['place']['comment']))
                                <p class="rating-comment text-muted">{{ $rate['place']['comment'] }}</p>
                                @endif
                                @endif
                                @if(!empty($rate['service']))
                                <div class="rating-header d-flex justify-content-between align-items-center mb-2">
                                    <div class="rating-label fw-bold text-blue">{{ __('site.service_rating') ?? 'الخدمة' }}</div>
                                    <div class="star-rating static text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star {{ $i <= ($rate['service']['rate'] ?? 0) ? '' : 'text-muted' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                @if(!empty($rate['service']['comment']))
                                <p class="rating-comment text-muted mb-0">{{ $rate['service']['comment'] }}</p>
                                @endif
                                @endif
                                @foreach($rate['replies'] ?? [] as $reply)
                                <div class="nested-reply mt-3">
                                    <div class="rating-header d-flex justify-content-between align-items-center mb-2">
                                        <div class="rating-label fw-bold text-dark">{{ $providerName ?? __('panel.provider') }}</div>
                                        <div class="rate-date">{{ $reply['created_at'] ?? '' }}</div>
                                    </div>
                                    <p class="rating-comment text-muted mb-0">{{ $reply['comment'] ?? '' }}</p>
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
        <!-- Start Media Modal -->
        <div class="modal fade" id="mediaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content bg-transparent border-0">
                    <div class="modal-header border-0 justify-content-end p-0 mb-2">
                        <button type="button" class="btn-close btn-close-white modal-invert-close"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-0" id="mediaModalBody">
                    </div>
                </div>
            </div>
        </div>
        <!-- End Media Modal -->
    </div>
@if($seats->isNotEmpty())
@push('scripts')
<style>
#salon-services-container.search-active .chair-tabs,
#salon-services-container.search-active .category-filters { display: none !important; }
.salon-search { position: relative; }
.salon-search .search-icon { inset-inline-start: 20px; pointer-events: none; }
.salon-search .search-reset-icon {
    position: absolute;
    inset-inline-end: 20px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: var(--bs-body-color);
    opacity: 0.6;
    font-size: 18px;
}
.salon-search .search-reset-icon:hover { opacity: 1; }
.salon-search input:placeholder-shown ~ .search-reset-icon { display: none !important; }
.salon-search input:not(:placeholder-shown) ~ .search-icon { display: none; }
.salon-search input:not(:placeholder-shown) ~ .search-reset-icon { display: block !important; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('salon-services-container');

    function updateSearchActiveState() {
        if (!container) return;
        var anySearching = false;
        document.querySelectorAll('.service-search-input').forEach(function(inp) {
            if ((inp.value || '').trim() !== '') anySearching = true;
        });
        container.classList.toggle('search-active', anySearching);
    }

    document.querySelectorAll('.service-search-input').forEach(function(input) {
        var pane = input.closest('.tab-pane');
        var categoryFilters = pane ? pane.querySelector('.category-filters') : null;
        var servicesList = pane ? pane.querySelector('.services-list') : null;
        var resetIcon = input.closest('.salon-search') ? input.closest('.salon-search').querySelector('.search-reset-icon') : null;

        function filterServices() {
            var q = (input.value || '').trim().toLowerCase();
            if (!servicesList) return;
            var cards = servicesList.querySelectorAll('.service-card');
            if (q === '') {
                cards.forEach(function(c) { c.style.display = ''; });
                if (categoryFilters) categoryFilters.style.display = 'flex';
                var activeBtn = categoryFilters ? categoryFilters.querySelector('.category-filter-btn.active') : null;
                if (activeBtn) {
                    var fv = activeBtn.getAttribute('data-filter');
                    cards.forEach(function(c) {
                        var cat = c.getAttribute('data-category');
                        c.style.display = (fv === 'all' || fv === cat) ? 'block' : 'none';
                    });
                }
                updateSearchActiveState();
            } else {
                if (container) container.classList.add('search-active');
                cards.forEach(function(c) {
                    var nameEl = c.querySelector('.service-name');
                    var name = (nameEl ? nameEl.textContent : '').toLowerCase();
                    var descEl = c.querySelector('.service-desc');
                    var desc = (descEl ? descEl.textContent : '').toLowerCase();
                    c.style.display = (name.indexOf(q) !== -1 || desc.indexOf(q) !== -1) ? 'block' : 'none';
                });
            }
        }

        input.addEventListener('input', filterServices);
        input.addEventListener('keyup', filterServices);

        if (resetIcon) {
            resetIcon.addEventListener('click', function() {
                input.value = '';
                input.focus();
                filterServices();
            });
            resetIcon.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    resetIcon.click();
                }
            });
        }
    });
});
</script>
@endpush
@endif
@if($hasCoupons)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Swiper === 'undefined') {
        return;
    }
    document.querySelectorAll('.coupon-provider-section .coupon-swiper-outer').forEach(function(outer) {
        var swiperEl = outer.querySelector('.provider-coupons-swiper');
        var pagEl = swiperEl ? swiperEl.querySelector('.coupon-provider-pagination') : null;
        if (!swiperEl || !pagEl) {
            return;
        }
        function syncCouponPaginationBullets(sw) {
            var bullets = pagEl.querySelectorAll('.swiper-pagination-bullet');
            if (!bullets.length) {
                return;
            }
            var idx = typeof sw.realIndex === 'number' ? sw.realIndex : sw.activeIndex;
            bullets.forEach(function (b, i) {
                var isOn = i === idx;
                b.classList.toggle('swiper-pagination-bullet-active', isOn);
                if (isOn) {
                    b.setAttribute('aria-current', 'true');
                } else {
                    b.removeAttribute('aria-current');
                }
            });
        }
        new Swiper(swiperEl, {
            slidesPerView: 1,
            spaceBetween: 16,
            watchOverflow: true,
            watchSlidesProgress: true,
            pagination: {
                el: pagEl,
                clickable: true,
                type: 'bullets',
                dynamicBullets: false
            },
            breakpoints: {
                992: { slidesPerView: 2, spaceBetween: 20 }
            },
            on: {
                init: function (sw) {
                    syncCouponPaginationBullets(sw);
                },
                slideChange: function (sw) {
                    syncCouponPaginationBullets(sw);
                },
                slideChangeTransitionEnd: function (sw) {
                    try {
                        if (sw.pagination && typeof sw.pagination.update === 'function') {
                            sw.pagination.update();
                        }
                    } catch (e) { /* noop */ }
                    syncCouponPaginationBullets(sw);
                }
            }
        });
    });
    document.querySelectorAll('.copy-coupon-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var code = this.getAttribute('data-code');
            var copyText = this.getAttribute('data-copy-text') || 'Copy';
            var copiedText = this.getAttribute('data-copied-text') || 'Copied!';
            function showCopied() {
                btn.textContent = copiedText;
                btn.classList.remove('btn-blue');
                btn.classList.add('btn-green');
                setTimeout(function() {
                    btn.textContent = copyText;
                    btn.classList.remove('btn-green');
                    btn.classList.add('btn-blue');
                }, 1000);
            }
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(code).then(showCopied);
            } else {
                var ta = document.createElement('textarea');
                ta.value = code;
                ta.style.position = 'fixed';
                ta.style.left = '-9999px';
                document.body.appendChild(ta);
                ta.select();
                try {
                    document.execCommand('copy');
                    showCopied();
                } catch (e) {}
                document.body.removeChild(ta);
            }
        });
    });
});
</script>
@endpush
@endif
@if($media->count() > 1)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Swiper !== 'undefined' && document.querySelector('.salon-hero-swiper')) {
        new Swiper('.salon-hero-swiper', { loop: true, pagination: { el: '.salon-hero-swiper .swiper-pagination' }, autoplay: { delay: 4000 } });
    }
});
</script>
@endpush
@endif
@endsection

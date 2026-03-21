@extends('site.layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $reservation = $reservation ?? null;
    $provider = $reservation?->reservable;
    $statusClass = match($reservation?->status?->value ?? '') {
        'pending' => 'pending',
        'processing' => 'processing',
        'completed' => 'completed',
        'canceled', 'not_performed' => 'canceled',
        default => 'pending',
    };
@endphp
<div class="container">
    <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('site.bookings') }}">{{ __('site.heading.my_bookings') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.booking_details') }}</li>
        </ol>
    </nav>
</div>

<div class="booking-details-section pb-64">
    @if(session('rating_success'))
        <div class="container"><div class="alert alert-success">{{ session('rating_success') }}</div></div>
    @endif
    <div class="container">
        
        <div class="row mb-4">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="data-card">
                    <h3 class="data-card-title">{{ __('site.heading.reservation_info') }}</h3>
                    <div class="data-list">
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.fields.reservation_number') }}</span>
                            <span class="value">{{ $reservation->id }}</span>
                        </div>
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.reservation_created_at') }}</span>
                            <span class="value">{{ $reservation->created_at?->format('Y/m/d') }} - {{ $reservation->created_at?->format('H:i') }}</span>
                        </div>
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.fields.status') }}</span>
                            <span class="status-badge {{ $statusClass }}">{{ $reservation->status?->getLabel() ?? '—' }}</span>
                        </div>
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.reservation_execution_date') }}</span>
                            <span class="value">{{ $reservation->date?->format('Y/m/d') ?? '—' }}</span>
                        </div>
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.reservation_execution_time') }}</span>
                            <span class="value">{{ $reservation->from?->format('H:i') ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 mt-4">
                <div class="data-card">
                    <h3 class="data-card-title">{{ __('site.heading.operator_details') }}</h3>
                    <div class="data-list custom-data-list">
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.fields.provider_name') }}</span>
                            <span class="value">{{ $provider?->getTranslation('name', $locale) ?? $provider?->name ?? '—' }}</span>
                        </div>
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.fields.city') }}</span>
                            <span class="value">{{ $provider?->city?->getTranslation('name', $locale) ?? $provider?->city?->name ?? '—' }}</span>
                        </div>
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.show_on_map') }}</span>
                            <a href="{{ $provider ? route('site.provider.map', $provider->id) : '#' }}" class="value text-danger text-decoration-underline">{{ __('site.heading.show_on_map') }}</a>
                        </div>
                        @if(!empty($workingDaysList))
                        <div class="data-item working-hours">
                            <span class="label flex-shrink-0"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.working_hours') }}</span>
                            <div class="d-flex flex-wrap">
                                @foreach($workingDaysList as $dayHours)
                                <span class="value">{{ $dayHours }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if($reservation->seat)
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.chair_name') }}</span>
                            <span class="value">{{ $reservation->seat->getTranslation('title', $locale) ?? $reservation->seat->title ?? '—' }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($serviceRating && $placeRating)
        <div class="data-card mb-4" id="order-rating-card">
            <h3 class="data-card-title">{{ __('site.heading.order_rate') }}</h3>
            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0 border-end-md">
                    <div class="rating-display-item">
                        <div class="rating-header d-flex gap-4 align-items-center mb-2">
                            <div class="rating-label"><i class="fa-solid fa-angles-left me-2"></i>{{ __('site.heading.service') }}</div>
                            <div class="star-rating static">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ ($serviceRating->rate ?? 0) >= $i ? 'star-active' : '' }}"></i>
                                @endfor
                            </div>
                        </div>
                        @if($serviceRating->comment)
                        <p class="rating-comment text-muted mb-0">{{ $serviceRating->comment }}</p>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="rating-display-item">
                        <div class="rating-header d-flex gap-4 align-items-center mb-2">
                            <div class="rating-label"><i class="fa-solid fa-angles-left me-2"></i>{{ __('site.heading.place') }}</div>
                            <div class="star-rating static">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ ($placeRating->rate ?? 0) >= $i ? 'star-active' : '' }}"></i>
                                @endfor
                            </div>
                        </div>
                        @if($placeRating->comment)
                        <p class="rating-comment text-muted mb-0">{{ $placeRating->comment }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row mb-4">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="data-card">
                    <h3 class="data-card-title">{{ __('site.heading.list_of_services') }}</h3>
                    <div class="data-list">
                        @forelse($servicesList ?? [] as $item)
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ $item['name'] ?? '—' }}</span>
                            <span class="price">{{ $item['price'] ?? '—' }} <i class="icon-saudi_riyal"></i></span>
                        </div>
                        @empty
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> —</span>
                            <span class="price">{{ $reservation->price->formatByDecimal() ?? '—' }} <i class="icon-saudi_riyal"></i></span>
                        </div>
                        @endforelse
                    </div>
                    @if($totalDuration > 0)
                    <div class="total-duration-row">
                        <span class="label">{{ __('site.heading.total_execution_time') }}</span>
                        <span class="value">{{ $totalDuration }} {{ __('site.minutes') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-6">
                <div class="data-card">
                    <h3 class="data-card-title">{{ __('site.heading.payment_details') }}</h3>
                    <div class="data-list">
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.payment_method') }}</span>
                            <span class="value">{{ $paymentMethod ?? '—' }}</span>
                        </div>
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.paid_amount') }}</span>
                            <span class="value">{{ $paidAmount ?? $reservation->price->formatByDecimal() }} <i class="icon-saudi_riyal"></i></span>
                        </div>
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.fields.download_invoice') }}</span>
                            <a href="{{ route('reservations.invoice', $reservation) }}" class="value text-danger text-decoration-underline" target="_blank">{{ __('site.fields.download_file') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($totals))
        <div class="row">
            <div class="col-12">
                <div class="data-card cost-details">
                    <h3 class="data-card-title mb-4">{{ __('site.heading.cost_details') }}</h3>
                    <div class="grid-cost-list">
                        @if(!empty($totals['services_total']) && $totals['services_total'] != '0.00')
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.total_services') }}</span>
                            <span class="value">{{ $totals['services_total'] ?? '0' }} <i class="icon-saudi_riyal"></i></span>
                        </div>
                        @endif
                        @if(!empty($totals['products_total']) && $totals['products_total'] != '0.00')
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.total_products') }}</span>
                            <span class="value">{{ $totals['products_total'] ?? '0' }} <i class="icon-saudi_riyal"></i></span>
                        </div>
                        @endif
                        @if(!empty($totals['discount']) && $totals['discount'] != '0.00')
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.discount_code') }}</span>
                            <span class="value">-{{ $totals['discount'] ?? '0' }} <i class="icon-saudi_riyal"></i></span>
                        </div>
                        @endif
                        @if(isset($totals['subtotal']))
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.total_cost') }}</span>
                            <span class="value">{{ $totals['subtotal'] ?? '0' }} <i class="icon-saudi_riyal"></i></span>
                        </div>
                        @endif
                        @if(!empty($totals['reservation_fees']) && $totals['reservation_fees'] != '0.00')
                        <div class="data-item">
                            <span class="label"><i class="fa-solid fa-angles-left"></i> {{ __('site.heading.reservation_fees') }}</span>
                            <span class="value">{{ $totals['reservation_fees'] ?? '0' }} <i class="icon-saudi_riyal"></i></span>
                        </div>
                        @endif
                    </div>
                    <div class="cost-divider-horizontal"></div>
                    <div class="row g-0 cost-footer-row">
                        <div class="col-md-6 cost-col cost-col-right border-end-md">
                            <div class="final-total">
                                <span class="label">{{ __('site.heading.final_total') }}</span>
                                <span class="value">{{ $totals['total'] ?? $reservation->price->formatByDecimal() }} <i class="icon-saudi_riyal"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6 cost-col cost-col-left">
                            <div class="earned-points">
                                <span class="label">{{ __('site.heading.points_earned') }}</span>
                                <span class="value">{{ $pointsEarned ?? 0 }} {{ __('site.heading.point') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($canRate ?? false)
        <div class="rate-service mt-5 text-center">
            <button type="button" class="btn btn-green py-2 px-5" data-bs-toggle="modal" data-bs-target="#rating-modal">
                {{ __('forms.fields.service_rate') }}
            </button>
        </div>
        @endif
    </div>
</div>

@if($canRate ?? false)
@livewire('site.reservation-rate-modal', ['reservation' => $reservation])
@endif


@endsection

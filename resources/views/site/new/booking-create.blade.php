@extends('site.layouts.app')

@section('content')
@php $locale = app()->getLocale(); @endphp
<!-- Start Breadcrumb -->
<div class="container">
    <nav class="custom-breadcrumb-nav my-4" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('site.provider.show', $provider->id) }}">{{ $provider->getTranslation('name', $locale) }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.book_now') }}</li>
        </ol>
    </nav>
</div>
<!-- End Breadcrumb -->

<div class="booking-details-section pb-64">
    <div class="container">
        @livewire('site.booking-checkout-form', [
            'provider' => $provider,
            'seat' => $seat ?? [],
            'cartServices' => $cartServices ?? [],
            'totals' => $totals ?? [],
            'pointsEarned' => $pointsEarned ?? 0,
            'reservationFlow' => $reservationFlow ?? 'total',
            'completeOrderText' => $completeOrderText ?? '',
            'nextDays' => $nextDays ?? [],
            'workingDays' => $workingDays ?? [],
            'termsUrl' => $termsUrl ?? '#',
        ], 'booking-checkout-' . $provider->id . '-' . (($seat['id'] ?? null) ?: 0))
    </div>
</div>
@endsection

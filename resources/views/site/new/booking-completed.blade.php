@extends('site.layouts.app')

@section('content')
<!-- Start Breadcrumb -->
<div class="container">
        <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb" class="my-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.booking_completed') }}</li>
            </ol>
        </nav>
    </div>
    <!-- End Breadcrumb -->

    <div class="common-page-wrapper pb-64">
        <div class="container">


            <div class="book-success-wrapper">
                <div class="book-success-card">

                    <div class="success-icon-wrapper">
                        <i class="fa-solid fa-check success-check-icon"></i>
                    </div>

                    @if($status === 'success')
                        <h1 class="success-title">{{ __('site.heading.booking_completed') }}</h1>
                        <p class="success-subtitle">{{ __('site.heading.booking_completed_text', ['order_id' => $order_id ?? '-']) }}</p>
                        <div class="success-actions">
                            @if($order_id)
                                <a href="{{ route('site.booking.show', $order_id) }}" class="btn btn-green">{{ __('site.heading.booking_details') }}</a>
                            @endif
                            <a href="{{ route('site.home') }}" class="btn btn-blue">{{ __('site.heading.home') }}</a>
                        </div>
                    @else
                        <h1 class="success-title">{{ __('site.heading.booking_failed') }}</h1>
                        <p class="success-subtitle">{{ __('site.heading.booking_failed_text') }}</p>
                        <div class="success-actions">
                            <a href="{{ route('site.home') }}" class="btn btn-blue">{{ __('site.heading.home') }}</a>
                        </div>
                    @endif

                </div>
            </div>

        </div>

    </div>
@endsection

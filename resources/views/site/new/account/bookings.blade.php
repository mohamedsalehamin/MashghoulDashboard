@extends('site.layouts.app')

@section('content')
@php $locale = app()->getLocale(); @endphp
<!-- Start Breadcrumb -->
<div class="container">
    <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('site.account.info') }}">{{ __('site.heading.my_account') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.my_bookings') }}</li>
        </ol>
    </nav>
</div>
<!-- End Breadcrumb -->

<div class="common-page-wrapper pb-64">
    <div class="container">

        <div class="text-center">
            <h1 class="section-title mb-5">{{ __('site.heading.my_bookings') }}</h1>
        </div>
        @livewire('site.profile-reservations-list')
    </div>

</div>
@endsection

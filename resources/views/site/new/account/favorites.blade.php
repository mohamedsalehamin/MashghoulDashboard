@extends('site.layouts.app')

@section('content')
@php $locale = app()->getLocale(); $assetBase = asset('assets/site'); @endphp
<!-- Start Breadcrumb -->
<div class="container">
    <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.html">{{ __('site.heading.home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('site.account.info') }}">{{ __('site.heading.my_account') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.favorites') }}</li>
        </ol>
    </nav>
</div>
<!-- End Breadcrumb -->

<div class="common-page-wrapper pb-64">
    <div class="container">
        @livewire('site.profile-favorites-list')
    </div>
</div>
@endsection

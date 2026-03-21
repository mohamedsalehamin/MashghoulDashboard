@extends('site.layouts.app')

@section('content')
@if(session('error'))
    <div class="container">
        <div class="alert alert-warning">{{ session('error') }}</div>
    </div>
@endif
<div class="container">
    <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('site.join') }}">{{ __('site.heading.join_us_as_a_provider') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.provider_join_register_title') }}</li>
        </ol>
    </nav>
</div>

<div class="join-us-page pb-64">
    <div class="container mb-5">
        <div class="text-center mb-5">
            <h2 class="login-main-title">{{ __('site.heading.provider_join_register_title') }}</h2>
            <p class="text-muted">{{ __('site.heading.provider_join_register_subtitle') }}</p>
        </div>
        @livewire('site.provider-register-form')
    </div>
</div>
@endsection

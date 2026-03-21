@extends('site.layouts.app')

@section('content')
<div class="container">
    <nav class="custom-breadcrumb-nav my-4" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.join_us_as_a_provider') }}</li>
        </ol>
    </nav>
</div>

@if(session('error'))
    <div class="container">
        <div class="alert alert-warning">{{ session('error') }}</div>
    </div>
@endif

<div class="join-us-page pb-64">
    <div class="container mb-5">
        <div class="text-center mb-5">
            <h2 class="login-main-title">{{ __('site.heading.join_us_as_a_provider') }}</h2>
            <p class="text-muted mb-0 mt-2">{{ __('site.join.flow_intro') }}</p>
        </div>
    </div>

    @livewire('site.join-plan-selection')
</div>
@endsection

@extends('site.layouts.app')

@section('content')
<div class="common-page-wrapper login-page py-64 d-flex align-items-center min-vh-100">
    <div class="container">
        <div class="login-card-wrapper d-flex flex-column flex-lg-row align-items-lg-center">
            <div class="login-form-side col-lg-7 p-4 p-md-5 d-flex flex-column justify-content-center order-2 order-lg-1">
                <div class="text-center mb-5 mt-2 mt-lg-0">
                    <h2 class="login-main-title mb-2">{{ __('site.heading.welcome_to_mashghoul')}}</h2>
                    <p class="text-muted login-subtitle">{{ __('site.heading.login_subtitle')}}</p>
                </div>
                @livewire('site.login-form')
                <div class="text-center mt-3">
                    <a href="{{ route('site.home') }}" class="guest-link text-muted">{{ __('site.heading.continue_as_guest')}}</a>
                </div>
            </div>
            <div class="login-info-side col-lg-5 p-4 p-md-5 d-flex flex-column justify-content-center align-items-center text-center order-1 order-lg-2 mb-3 mb-lg-0">
                <img src="{{ asset('assets/site/images/logo.png') }}" alt="{{ config('app.name') }}" class="img-fluid mb-5 login-logo">
                <h3 class="text-white mb-4">{{ __('site.heading.no_account')}}</h3>
                <a href="{{ route('site.register') }}" class="btn btn-green create-account-btn px-5">{{ __('site.heading.create_account')}}</a>
            </div>
        </div>
    </div>
</div>
@endsection

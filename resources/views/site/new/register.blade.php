@extends('site.layouts.app')

@section('content')
<div class="common-page-wrapper login-page py-64 d-flex align-items-center min-vh-100">
    <div class="container">
        <div class="login-card-wrapper d-flex flex-column flex-lg-row align-items-lg-center">
            <div class="login-form-side col-lg-7 p-4 p-md-5 d-flex flex-column justify-content-center order-2 order-lg-1">
                <div class="text-center mb-5 mt-2 mt-lg-0">
                    <h2 class="login-main-title mb-2">{{ __('site.heading.register_as_customer') ?? 'إنشاء حساب' }}</h2>
                    <p class="text-muted login-subtitle">{{ __('site.heading.register_subtitle') ?? 'انضم لمشغول واحجز خدماتك بسهولة' }}</p>
                </div>
                @livewire('site.customer-register-form')
            </div>
            <div class="login-info-side col-lg-5 p-4 p-md-5 d-flex flex-column justify-content-center align-items-center text-center order-1 order-lg-2 mb-3 mb-lg-0">
                <img src="{{ asset('assets/site/images/logo.png') }}" alt="{{ config('app.name') }}" class="img-fluid mb-5 login-logo">
                <h3 class="text-white mb-4">{{ __('site.heading.already_have_account') ?? 'لديك حساب؟' }}</h3>
                <a href="{{ route('site.login') }}" class="btn btn-green create-account-btn px-5">{{ __('site.heading.login') ?? 'تسجيل الدخول' }}</a>
            </div>
        </div>
    </div>
</div>
@endsection

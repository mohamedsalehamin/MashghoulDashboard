@extends('site.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-4">
                <h1 class="h3 login-main-title">{{ __('site.join.payment_failed_title') }}</h1>
                <p class="text-muted">{{ __('site.join.payment_failed_body') }}</p>
                <p class="text-muted small">{{ __('site.join.payment_failed_login_hint') }}</p>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                @php
                    $portalLogin = \Filament\Facades\Filament::getPanel('lab-panel')->getLoginUrl();
                @endphp
                <a href="{{ $portalLogin }}" class="btn btn-green px-5">{{ __('site.heading.provider_portal_login') }}</a>
                <a href="{{ route('site.join') }}" class="btn btn-outline-secondary px-5">{{ __('site.join.try_again') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection

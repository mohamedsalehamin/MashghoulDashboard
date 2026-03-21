@extends('site.layouts.app')

@section('content')
 <!-- Start Breadcrumb -->
 <div class="container">
    <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('site.home') }}">{{ __('site.heading.home') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('site.heading.contact_us') }}</li>
        </ol>
    </nav>
</div>
<!-- End Breadcrumb -->

<div class="common-page-wrapper pb-64">
    <div class="container">
        <div class="text-center">
            <h1 class="section-title mb-5">{{ __('site.heading.contact_us') }}</h1>
        </div>
        <div class="row">
            <div class="col-lg-6  mb-4">
                @livewire('contact-us')
            </div>
            <div class="col-lg-5 offset-lg-1 mb-4">
                <div class="contact-info-box">
                    <div class="info-header">
                        <div class="corner-dot top-right"></div>
                        <div class="corner-dot top-left"></div>
                        <div class="corner-dot bottom-right"></div>
                        <div class="corner-dot bottom-left"></div>
                        <h3>{{ __('site.heading.our_team_is_ready_to_serve_you_and_answer_all_your_questions') }}</h3>
                    </div>
                    <div class="info-body">
                        <ul class="contact-list list-unstyled">
                            <li>
                                <a href="tel:{{ $settings->app_phone }}">
                                    <div class="contact-icon"><i class="fa-solid fa-mobile-screen"></i></div>
                                    <div class="contact-text">{{ $settings->app_phone }}</div>
                                </a>
                            </li>
                            <li>
                                <a href="https://wa.me/{{ $settings->app_whatsapp }}">
                                    <div class="contact-icon"><i class="fab fa-whatsapp"></i></div>
                                    <div class="contact-text">{{ $settings->app_whatsapp }}</div>
                                </a>
                            </li>
                            <li>
                                <a href="mailto:{{ $settings->app_email }}">
                                    <div class="contact-icon"><i class="fa-regular fa-envelope"></i></div>
                                    <div class="contact-text">{{ $settings->app_email }}</div>
                                </a>
                            </li>
                        </ul>
                        <div class="social-icons">
                            @foreach($settings->social_links as $social)
                                <a href="{{ $social['link'] }}" target="_blank"><i class="fab fa-{{Str::replace(" ","",$social['icon'])}}"></i></a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@extends('site.layouts.app')
@section("title",__('site.heading.contact_us'))
@php($breadcrumb= site()->breadcrumbs()->add(__('site.heading.contact_us')))
@section('content')

    <section class="page-content about-page">
        <div class="container">
            <h1 class="page-title">@lang('site.heading.contact_us')</h1>
            <div class="contact-row">
                <div class="contact-content">
                    <div class="contact-info">
                        <h3 class="contact-title">
                            @lang('site.heading.contact_us_text')

                        </h3>
                        <ul class="contacts-list">
                            <li>
                                <a href="tel:{{$settings->app_phone}}" class="contact-box">
                                    <i class="fa-regular fa-phone"></i>
                                    <span style="direction: ltr"> {{$settings->app_phone}} </span>
                                </a>

                            </li>
                            <li>
                                <a href="mailto:{{$settings->app_email}}" class="contact-box">
                                    <i class="fa-regular fa-envelope"></i>
                                    <span style="direction: ltr"> {{$settings->app_email}} </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="contact-socials">
                        <h3 class="contact-title">@lang('site.heading.follow_us_in_social')</h3>
                        <div class="socials">
                            @foreach($social_links as $social_link)
                                <a href="{{$social_link['link']}}" class="social">
                                    <i class="fa-brands fa-{{$social_link['icon']}}"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @livewire('contact-form')

                </div>
                <div class="contact-img">
                    <div class="contact-cover loading-img lazy-img-parent">
                        <img data-src="images/contact/1.jpg" class="lazy-img" />
                    </div>
                    <div class="contact-logo loading-img lazy-img-parent">
                        <img data-src="images/contact/logo.svg" class="lazy-img" />
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('site.layouts.app')
@section("title",__('site.heading.paid_successfully'))
@php($breadcrumb=site()->breadcrumbs()->add(__('site.heading.paid_successfully')))
@section('content')
    <section class="page-content success-page">
        <div class="container">
            <div class="success-content">
                <div class="success-icon">
                    <i class="fa-light fa-circle-check"></i>
                </div>
                <h1 class="success-title">@lang('site.heading.checkout_success_title')</h1>
                <p class="success-description">
                    @lang('site.heading.checkout_success_subtitle')<span>#{{session()->get('reservation_id')}}</span>
                    @lang('site.heading.checkout_success_subtitle2')
                </p>
                <a href="{{route('profile.reservations')}}" class="success-btn main-btn">
                    @lang('site.heading.my_reservations')
                </a>
            </div>
        </div>
    </section>

@endsection


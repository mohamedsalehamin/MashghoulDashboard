@extends('site.layouts.app')
@section("title",__('site.heading.paid_successfully'))
@php($breadcrumb=site()->breadcrumbs()->add(__('site.heading.error')))
@section('content')
    <section class="page-content success-page">
        <div class="container">
            <div class="success-content">
                <div class="success-icon text-danger">
                    <i class="fa-light fa-circle-x"></i>
                </div>
                <h1 class="success-title">@lang('site.heading.error')</h1>
                <p class="success-description">
                    @lang('site.heading.something_went_wrong')
                </p>
                <a href="{{route('home')}}" class="success-btn main-btn">
                    @lang('site.heading.home')
                </a>
            </div>
        </div>
    </section>

@endsection


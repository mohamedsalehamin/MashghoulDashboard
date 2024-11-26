@extends('site.layouts.app')
@section("title",__('site.heading.reservation_summary'))
@php($breadcrumb=site()->breadcrumbs()->add(__('site.heading.reservation_summary')))

@section('content')
    <section class="page-content checkout-page">
        @livewire('doctor-reservation-checkout-view')
    </section>

@endsection


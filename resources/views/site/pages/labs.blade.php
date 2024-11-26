@extends('site.layouts.app')
@section("title",__('site.heading.labs'))
@php($breadcrumb= site()->breadcrumbs()->add(__('site.heading.labs')))
@section('content')
    <section class="page-content doctors-page">
        <div class="container">
            <h1 class="page-title">@lang('site.heading.labs')</h1>
            @livewire('labs-list')
        </div>
    </section>
@endsection


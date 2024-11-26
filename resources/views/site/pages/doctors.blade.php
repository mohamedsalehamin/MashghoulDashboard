@extends('site.layouts.app')
@section("title",__('site.heading.doctors'))
@php($breadcrumb= site()->breadcrumbs()->add(__('site.heading.doctors')))
@section('content')
    <section class="page-content doctors-page">
        @livewire('doctors-list')
    </section>
@endsection
